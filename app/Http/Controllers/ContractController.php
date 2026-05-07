<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Client;
use App\Models\FinancialCategory;
use App\Models\FinancialWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, Contract $content)
    {

        $this->request = $request;
        $this->repository = $content;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.contracts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $wallets = FinancialWallet::where('status', true)->orderBy('name', 'ASC')->get();
        $categories = FinancialCategory::where('status', true)
            ->where('type', 'entrada')
            ->orderBy('name', 'ASC')
            ->get();

        // RENDER VIEW
        return view('pages.contracts.create')->with([
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration_in_months' => 'required|integer|min:1',
            'wallet_id' => 'required|exists:financial_wallets,id',
            'category_id' => 'required|exists:financial_categories,id',
        ]);

        // GET FORM DATA
        $data = $request->all();
        $data['period_in_months'] = 1;

        // CREATED BY
        $data['created_by'] = Auth::id();

        // SEND DATA
        $status = $this->repository->create($data);

        return redirect()
            ->route('contracts.index')
            ->with('message', 'Contrato criado com sucesso.');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // GET ALL DATA
        $content = $this->repository->find($id);
        $wallets = FinancialWallet::where('status', true)->orderBy('name', 'ASC')->get();
        $categories = FinancialCategory::where('status', true)
            ->where('type', 'entrada')
            ->orderBy('name', 'ASC')
            ->get();

        // VERIFY IF EXISTS
        if(!$content) return redirect()->back();

        // GENERATES DISPLAY WITH DATA
        return view('pages.contracts.edit')->with([
            'content' => $content,
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration_in_months' => 'required|integer|min:1',
            'wallet_id' => 'required|exists:financial_wallets,id',
            'category_id' => 'required|exists:financial_categories,id',
        ]);

        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id))
        return redirect()->back();

        // GET FORM DATA
        $data = $request->all();
        $data['period_in_months'] = 1;

        // UPDATE BY
        $data['updated_by'] = Auth::id();

        // STORING NEW DATA
        $content->update($data);

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('contracts.index')
            ->with('message', 'Contrato alterado com sucesso.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // GET DATA
        $content = $this->repository->find($id);
        $status = $content->status == true ? false : true;

        // STORING NEW DATA
        $this->repository->where('id', $id)->update(['status' => $status, 'updated_by' => Auth::id()]);

        return redirect()
            ->route('contracts.index')
            ->with('message', 'Contrato alterado com sucesso.');

    }

    public function delete($id)
    {
        $content = $this->repository->find($id);

        if (!$content) {
            return redirect()->route('contracts.index')->with('message', 'Contrato não encontrado.');
        }

        DB::transaction(function () use ($content) {
            Client::where('contract_id', $content->id)->update([
                'contract_id' => null,
                'updated_by' => Auth::id(),
            ]);

            $content->delete();
        });

        return redirect()->route('contracts.index')->with('message', 'Contrato excluído com sucesso.');
    }
}
