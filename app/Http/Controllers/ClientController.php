<?php

namespace App\Http\Controllers;

use App\Models\AgendaMember;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Contract;
use App\Models\FinancialCategory;
use App\Models\FinancialWallet;
use App\Models\FinancialTransaction;
use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Status;
use App\Services\ClientContractBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    protected $request;
    private $repository;
    private $billingService;

    public function __construct(Request $request, Client $content, ClientContractBillingService $billingService)
    {
        $this->request = $request;
        $this->repository = $content;
        $this->billingService = $billingService;
    }

    public function index()
    {
        $contracts = Contract::where('status', true)->orderBy('name', 'ASC')->get();

        return view('pages.clients.index')->with([
            'contracts' => $contracts,
        ]);
    }

    public function create()
    {
        $projectTypes = ProjectType::where('status', true)->orderBy('name', 'ASC')->get();

        return view('pages.clients.create')->with([
            'projectTypes' => $projectTypes,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by'] = Auth::id();

        if ($request->boolean('create_project')) {
            $request->validate([
                'project_name' => 'required|string|max:255',
                'project_type_id' => 'required|exists:projects_types,id',
            ]);
        }

        if (!empty($data['payment_day'])) {
            $request->validate([
                'payment_day' => 'nullable|integer|min:1|max:28',
            ]);
        }

        DB::transaction(function () use ($data, $request) {
            $client = $this->repository->create($data);

            if (!$request->boolean('create_project')) {
                return;
            }

            $project = Project::create([
                'name' => $request->project_name,
                'type_is' => 'time',
                'type_id' => $request->project_type_id,
                'created_by' => Auth::id(),
            ]);

            $project->users()->sync([Auth::id()]);

            Module::create([
                'name' => 'Módulo Inicial',
                'project_id' => $project->id,
                'color' => '#348feb',
                'created_by' => Auth::id(),
            ]);

            Status::create([
                'name' => 'Não Iniciado',
                'color' => '#365e92',
                'project_id' => $project->id,
                'order' => 1,
                'created_by' => Auth::id(),
            ]);

            Status::create([
                'name' => 'Parado',
                'color' => '#D83F58',
                'project_id' => $project->id,
                'order' => 2,
                'created_by' => Auth::id(),
            ]);

            Status::create([
                'name' => 'Em andamento',
                'color' => '#F4A541',
                'project_id' => $project->id,
                'order' => 3,
                'created_by' => Auth::id(),
            ]);

            Status::create([
                'name' => 'Feito',
                'color' => '#63BC07',
                'project_id' => $project->id,
                'order' => 4,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()
                ->route('clients.index')
                ->with('message', 'Cliente adicionado com sucesso.');
    }

    public function edit($id)
    {
        $content = $this->repository->find($id);
        $contracts = Contract::where('status', true)->orderBy('id', 'ASC')->get();

        if (!$content) {
            return redirect()->back();
        }

        $activeClientContract = ClientContract::with('contract')
            ->where('client_id', $content->id)
            ->where('status', true)
            ->orderByDesc('id')
            ->first();
        $clientContracts = ClientContract::with('contract')
            ->where('client_id', $content->id)
            ->orderByDesc('id')
            ->get();

        $billings = FinancialTransaction::where('client_id', $content->id)
            ->where('type', 'entrada')
            ->orderBy('due_date', 'ASC')
            ->orderBy('date', 'ASC')
            ->limit(200)
            ->get();

        $wallets = FinancialWallet::where('status', true)->orderBy('name', 'ASC')->get();
        $categories = FinancialCategory::where('status', true)
            ->where('type', 'entrada')
            ->orderBy('name', 'ASC')
            ->get();

        return view('pages.clients.edit')->with([
            'content' => $content,
            'contracts' => $contracts,
            'activeClientContract' => $activeClientContract,
            'clientContracts' => $clientContracts,
            'billings' => $billings,
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!$content = $this->repository->find($id)) {
            return redirect()->back();
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        if (!empty($data['payment_day'])) {
            $request->validate([
                'payment_day' => 'nullable|integer|min:1|max:28',
            ]);
        }

        $content->update($data);

        return redirect()
            ->route('clients.edit', $content->id)
            ->with('message', 'Cliente atualizado com sucesso.');
    }

    public function storeClientContract(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return redirect()->route('clients.index')->with('message', 'Cliente não encontrado.');
        }

        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'amount' => 'required',
            'start_date' => 'required|date',
            'confirm_contract_generation' => 'required|accepted',
        ]);

        $contract = Contract::where('status', true)->find($request->contract_id);

        if (!$contract) {
            return redirect()->route('clients.edit', $client->id)->with('message', 'Contrato inválido.');
        }

        $activeContract = ClientContract::where('client_id', $client->id)
            ->where('status', true)
            ->orderByDesc('id')
            ->first();

        if ($activeContract) {
            $activeContract->status = false;
            $activeContract->end_date = now()->toDateString();
            $activeContract->updated_by = Auth::id();
            $activeContract->save();
        }

        $amountValue = preg_replace('/[^\d,\.]/', '', $request->amount);
        $amountValue = str_replace(',', '.', $amountValue);
        $amountValue = str_replace(' ', '', $amountValue);

        $clientContract = ClientContract::create([
            'client_id' => $client->id,
            'contract_id' => $contract->id,
            'amount' => $amountValue,
            'start_date' => $request->start_date,
            'period_in_months' => 1,
            'duration_in_months' => $contract->duration_in_months,
            'status' => true,
            'created_by' => Auth::id(),
        ]);

        $this->billingService->generateRecurringTransactions($clientContract->load(['contract', 'client']), Auth::id());

        return redirect()->route('clients.edit', $client->id)->with('message', 'Contrato vinculado e cobranças geradas com sucesso.');
    }

    public function closeClientContract($id, $clientContractId)
    {
        $client = Client::find($id);

        if (!$client) {
            return redirect()->route('clients.index')->with('message', 'Cliente não encontrado.');
        }

        $clientContract = ClientContract::where('id', $clientContractId)
            ->where('client_id', $client->id)
            ->first();

        if (!$clientContract) {
            return redirect()->route('clients.edit', $client->id)->with('message', 'Contrato não encontrado.');
        }

        $clientContract->status = false;
        $clientContract->end_date = now()->toDateString();
        $clientContract->updated_by = Auth::id();
        $clientContract->save();

        return redirect()->route('clients.edit', $client->id)->with('message', 'Contrato encerrado com sucesso.');
    }

    public function contractPreview(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'amount' => 'required',
            'start_date' => 'nullable',
        ]);

        $contract = Contract::find($request->contract_id);

        if (!$contract) {
            return response()->json(['message' => 'Contrato inválido.'], 422);
        }

        $duration = max($contract->duration_in_months, 1);
        $total = $duration;

        if ($total < 1) {
            $total = 1;
        }

        return response()->json([
            'contract_name' => $contract->name,
            'duration_in_months' => $duration,
            'amount' => $request->amount,
            'start_date' => $request->start_date ?: now()->format('d/m/Y'),
            'total_transactions' => $total,
        ]);
    }

    public function toggleBillingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pendente,pago',
        ]);

        $transaction = FinancialTransaction::where('id', $id)
            ->where('type', 'entrada')
            ->first();

        if (!$transaction) {
            return redirect()->back()->with('message', 'Cobrança não encontrada.');
        }

        $status = $request->status;

        $transaction->billing_status = $status;
        $transaction->paid_at = null;

        if ($status === 'pago') {
            $transaction->paid_at = now();
        }

        $transaction->updated_by = Auth::id();
        $transaction->save();

        return redirect()->back()->with('message', 'Status da cobrança atualizado com sucesso.');
    }

    public function addAdhocBilling(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return redirect()->route('clients.index')->with('message', 'Cliente não encontrado.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'wallet_id' => 'required|exists:financial_wallets,id',
            'category_id' => 'required|exists:financial_categories,id',
            'date' => 'required|date',
            'amount' => 'required',
            'description' => 'nullable|string',
        ]);

        $amountValue = preg_replace('/[^\d,\.]/', '', $request->amount);
        $amountValue = str_replace(',', '.', $amountValue);
        $amountValue = str_replace(' ', '', $amountValue);

        FinancialTransaction::create([
            'type' => 'entrada',
            'origin_type' => 'avulsa',
            'billing_status' => 'pendente',
            'name' => $request->name,
            'wallet_id' => $request->wallet_id,
            'category_id' => $request->category_id,
            'client_id' => $client->id,
            'counterparty_type' => 'client',
            'counterparty_id' => $client->id,
            'date' => $request->date,
            'due_date' => $request->date,
            'amount' => $amountValue,
            'description' => $request->description,
            'status' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('clients.edit', $client->id)->with('message', 'Cobrança avulsa criada com sucesso.');
    }

    public function destroy($id)
    {
        $content = $this->repository->find($id);
        $status = $content->status == true ? false : true;

        $this->repository->where('id', $id)->update(['status' => $status, 'updated_by' => Auth::id()]);

        return redirect()
            ->route('clients.index')
            ->with('message', 'Cliente ' . ($status == false ? 'desativado' : 'habilitado') . ' com sucesso.');
    }

    public function delete($id)
    {
        $content = $this->repository->find($id);

        if (!$content) {
            return redirect()
                ->route('clients.index')
                ->with('message', 'Cliente não encontrado.');
        }

        DB::transaction(function () use ($content) {
            AgendaMember::where('type', 'client')
                ->where('member_id', $content->id)
                ->delete();

            $content->delete();
        });

        return redirect()
            ->route('clients.index')
            ->with('message', 'Cliente excluído com sucesso.');
    }

}
