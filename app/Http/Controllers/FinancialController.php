<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\FinancialWallet;
use App\Models\User;
use App\Services\ClientContractBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{
    private $billingService;

    public function __construct(ClientContractBillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    private function ensureAdmin(): void
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();
        $wallets = FinancialWallet::where('status', 1)->orderBy('name')->get();
        $categories = FinancialCategory::where('status', 1)->orderBy('name')->get();

        return view('pages.financial.index', compact('wallets', 'categories'));
    }

    public function create()
    {
        $this->ensureAdmin();
        $wallets = FinancialWallet::where('status', 1)->orderBy('name')->get();
        $categories = FinancialCategory::where('status', 1)->orderBy('name')->get();
        $clients = Client::where('status', 1)->orderBy('name')->get();

        return view('pages.financial.create', compact('wallets', 'categories', 'clients'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'type' => 'required|in:entrada,debito',
            'name' => 'required|string|max:255',
            'origin_type' => 'nullable|in:recorrente,avulsa',
            'billing_status' => 'nullable|in:pendente,pago,vencido,cancelado',
            'wallet_id' => 'required|exists:financial_wallets,id',
            'category_id' => 'required|exists:financial_categories,id',
            'counterparty_type' => 'nullable|in:client,user',
            'counterparty_id' => 'nullable|integer',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'paid_at' => 'nullable|date',
            'amount' => 'required',
            'description' => 'nullable|string',
        ]);

        $category = FinancialCategory::find($data['category_id']);
        if (!$category || $category->type !== $data['type']) {
            return redirect()->back()->withInput()->with('message', 'A categoria selecionada não pertence a este tipo de transação.');
        }

        if (!empty($data['counterparty_type']) && empty($data['counterparty_id'])) {
            return redirect()->back()->withInput()->with('message', 'Selecione o favorecido.');
        }

        if (!empty($data['counterparty_type']) && !empty($data['counterparty_id'])) {
            if ($data['counterparty_type'] === 'client' && !Client::where('id', $data['counterparty_id'])->exists()) {
                return redirect()->back()->withInput()->with('message', 'Favorecido cliente inválido.');
            }
            if ($data['counterparty_type'] === 'user' && !User::where('id', $data['counterparty_id'])->exists()) {
                return redirect()->back()->withInput()->with('message', 'Favorecido membro inválido.');
            }
        }

        $data['client_id'] = ($data['counterparty_type'] ?? null) === 'client'
            ? ($data['counterparty_id'] ?? null)
            : null;
        $data['origin_type'] = $data['origin_type'] ?? 'avulsa';
        $data['billing_status'] = $data['billing_status'] ?? 'pendente';
        $data['due_date'] = $data['due_date'] ?? $data['date'];

        $data['amount'] = (float) str_replace([',', ' '], ['.', ''], preg_replace('/[^\d,\.]/', '', (string) $data['amount']));
        $data['created_by'] = Auth::id();

        FinancialTransaction::create($data);

        return redirect()->route('financial.index')->with('message', 'Transação cadastrada com sucesso.');
    }

    public function edit($id)
    {
        $this->ensureAdmin();
        $content = FinancialTransaction::find($id);
        if (!$content) {
            if (request()->boolean('json') || request()->expectsJson()) {
                return response()->json(['message' => 'Transação não encontrada.'], 404);
            }
            return redirect()->route('financial.index');
        }

        if (request()->boolean('json') || request()->expectsJson()) {
            return response()->json([
                'id' => $content->id,
                'type' => $content->type,
                'name' => $content->name,
                'wallet_id' => $content->wallet_id,
                'category_id' => $content->category_id,
                'counterparty_type' => $content->counterparty_type,
                'counterparty_id' => $content->counterparty_id,
                'date' => optional($content->date)->format('Y-m-d'),
                'created_at' => optional($content->created_at)->format('d/m/Y H:i'),
                'amount' => number_format((float) $content->amount, 2, '.', ''),
                'description' => $content->description,
            ]);
        }

        $wallets = FinancialWallet::where('status', 1)->orderBy('name')->get();
        $categories = FinancialCategory::where('status', 1)->orderBy('name')->get();
        $clients = Client::where('status', 1)->orderBy('name')->get();

        return view('pages.financial.edit', compact('content', 'wallets', 'categories', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin();
        $content = FinancialTransaction::find($id);
        if (!$content) return redirect()->route('financial.index');

        $data = $request->validate([
            'type' => 'required|in:entrada,debito',
            'name' => 'required|string|max:255',
            'origin_type' => 'nullable|in:recorrente,avulsa',
            'billing_status' => 'nullable|in:pendente,pago,vencido,cancelado',
            'wallet_id' => 'required|exists:financial_wallets,id',
            'category_id' => 'required|exists:financial_categories,id',
            'counterparty_type' => 'nullable|in:client,user',
            'counterparty_id' => 'nullable|integer',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'paid_at' => 'nullable|date',
            'amount' => 'required',
            'description' => 'nullable|string',
        ]);

        $category = FinancialCategory::find($data['category_id']);
        if (!$category || $category->type !== $data['type']) {
            return redirect()->back()->withInput()->with('message', 'A categoria selecionada não pertence a este tipo de transação.');
        }

        if (!empty($data['counterparty_type']) && empty($data['counterparty_id'])) {
            return redirect()->back()->withInput()->with('message', 'Selecione o favorecido.');
        }

        if (!empty($data['counterparty_type']) && !empty($data['counterparty_id'])) {
            if ($data['counterparty_type'] === 'client' && !Client::where('id', $data['counterparty_id'])->exists()) {
                return redirect()->back()->withInput()->with('message', 'Favorecido cliente inválido.');
            }
            if ($data['counterparty_type'] === 'user' && !User::where('id', $data['counterparty_id'])->exists()) {
                return redirect()->back()->withInput()->with('message', 'Favorecido membro inválido.');
            }
        }

        $data['client_id'] = ($data['counterparty_type'] ?? null) === 'client'
            ? ($data['counterparty_id'] ?? null)
            : null;
        $data['origin_type'] = $data['origin_type'] ?? 'avulsa';
        $data['billing_status'] = $data['billing_status'] ?? 'pendente';
        $data['due_date'] = $data['due_date'] ?? $data['date'];

        $data['amount'] = (float) str_replace([',', ' '], ['.', ''], preg_replace('/[^\d,\.]/', '', (string) $data['amount']));
        $data['updated_by'] = Auth::id();

        $content->update($data);

        return redirect()->route('financial.index')->with('message', 'Transação atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $this->ensureAdmin();
        $content = FinancialTransaction::find($id);
        if (!$content) return redirect()->route('financial.index');

        $content->status = !$content->status;
        $content->updated_by = Auth::id();
        $content->save();

        return redirect()->route('financial.index')->with('message', 'Transação atualizada com sucesso.');
    }

    public function delete($id)
    {
        $this->ensureAdmin();
        $content = FinancialTransaction::find($id);
        if (!$content) return redirect()->route('financial.index');

        $content->delete();

        return redirect()->route('financial.index')->with('message', 'Transação excluída com sucesso.');
    }

    public function counterparties($type)
    {
        $this->ensureAdmin();
        if ($type === 'client') {
            return response()->json(Client::where('status', 1)->orderBy('name')->get(['id', 'name']));
        }

        if ($type === 'user') {
            return response()->json(User::where('status', 1)->orderBy('name')->get(['id', 'name']));
        }

        return response()->json([]);
    }

    public function materializeProjectedBilling(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'client_contract_id' => 'required|integer',
            'reference_period' => 'required|string|size:7',
        ]);

        $clientContract = ClientContract::with(['contract', 'client'])
            ->where('id', $request->client_contract_id)
            ->where('status', true)
            ->first();

        if (!$clientContract) {
            return response()->json([
                'message' => 'Contrato vinculado não encontrado para materializar a cobrança.',
            ], 422);
        }

        $this->billingService->materializeReferencePeriod(
            $clientContract,
            $request->reference_period,
            Auth::id(),
            'pago'
        );

        return response()->json([
            'message' => 'Cobrança projetada materializada e marcada como paga com sucesso.',
        ]);
    }

    public function wallets()
    {
        $this->ensureAdmin();
        $contents = FinancialWallet::orderBy('status', 'desc')->orderBy('id', 'desc')->get();
        return view('pages.financial.wallets.index', compact('contents'));
    }

    public function walletsCreate()
    {
        $this->ensureAdmin();
        return view('pages.financial.wallets.create');
    }

    public function walletsStore(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate(['name' => 'required|string|max:255']);
        $data['created_by'] = Auth::id();
        FinancialWallet::create($data);
        return redirect()->route('financial.wallets.index')->with('message', 'Carteira criada com sucesso.');
    }

    public function walletsEdit($id)
    {
        $this->ensureAdmin();
        $content = FinancialWallet::find($id);
        if (!$content) return redirect()->route('financial.wallets.index');
        return view('pages.financial.wallets.edit', compact('content'));
    }

    public function walletsUpdate(Request $request, $id)
    {
        $this->ensureAdmin();
        $content = FinancialWallet::find($id);
        if (!$content) return redirect()->route('financial.wallets.index');

        $data = $request->validate(['name' => 'required|string|max:255']);
        $data['updated_by'] = Auth::id();
        $content->update($data);

        return redirect()->route('financial.wallets.index')->with('message', 'Carteira atualizada com sucesso.');
    }

    public function walletsDestroy($id)
    {
        $this->ensureAdmin();
        $content = FinancialWallet::find($id);
        if (!$content) return redirect()->route('financial.wallets.index');

        $content->status = !$content->status;
        $content->updated_by = Auth::id();
        $content->save();

        return redirect()->route('financial.wallets.index')->with('message', 'Carteira atualizada com sucesso.');
    }

    public function walletsDelete($id)
    {
        $this->ensureAdmin();
        $content = FinancialWallet::find($id);
        if (!$content) return redirect()->route('financial.wallets.index');

        if (FinancialTransaction::where('wallet_id', $id)->exists()) {
            return redirect()->route('financial.wallets.index')->with('message', 'Esta carteira possui transações vinculadas. Inative em vez de excluir.');
        }

        $content->delete();
        return redirect()->route('financial.wallets.index')->with('message', 'Carteira excluída com sucesso.');
    }

    public function categories()
    {
        $this->ensureAdmin();
        $contents = FinancialCategory::orderBy('status', 'desc')->orderBy('id', 'desc')->get();
        return view('pages.financial.categories.index', compact('contents'));
    }

    public function categoriesCreate()
    {
        $this->ensureAdmin();
        return view('pages.financial.categories.create');
    }

    public function categoriesStore(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:entrada,debito',
        ]);
        $data['created_by'] = Auth::id();
        FinancialCategory::create($data);
        return redirect()->route('financial.categories.index')->with('message', 'Categoria criada com sucesso.');
    }

    public function categoriesEdit($id)
    {
        $this->ensureAdmin();
        $content = FinancialCategory::find($id);
        if (!$content) return redirect()->route('financial.categories.index');
        return view('pages.financial.categories.edit', compact('content'));
    }

    public function categoriesUpdate(Request $request, $id)
    {
        $this->ensureAdmin();
        $content = FinancialCategory::find($id);
        if (!$content) return redirect()->route('financial.categories.index');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:entrada,debito',
        ]);
        $data['updated_by'] = Auth::id();
        $content->update($data);

        return redirect()->route('financial.categories.index')->with('message', 'Categoria atualizada com sucesso.');
    }

    public function categoriesDestroy($id)
    {
        $this->ensureAdmin();
        $content = FinancialCategory::find($id);
        if (!$content) return redirect()->route('financial.categories.index');

        $content->status = !$content->status;
        $content->updated_by = Auth::id();
        $content->save();

        return redirect()->route('financial.categories.index')->with('message', 'Categoria atualizada com sucesso.');
    }

    public function categoriesDelete($id)
    {
        $this->ensureAdmin();
        $content = FinancialCategory::find($id);
        if (!$content) return redirect()->route('financial.categories.index');

        if (FinancialTransaction::where('category_id', $id)->exists()) {
            return redirect()->route('financial.categories.index')->with('message', 'Esta categoria possui transações vinculadas. Inative em vez de excluir.');
        }

        $content->delete();
        return redirect()->route('financial.categories.index')->with('message', 'Categoria excluída com sucesso.');
    }
}
