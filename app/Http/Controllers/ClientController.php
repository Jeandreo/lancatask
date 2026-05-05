<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\AgendaMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, Client $content)
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
        $contracts = Contract::where('status', true)->orderBy('name', 'ASC')->get();

        // RETURN VIEW WITH DATA
        return view('pages.clients.index')->with([
            'contracts' => $contracts,
        ]);

    }

    public function processing(Request $request)
    {
        $query = $this->repository
            ->leftJoin('contracts', 'contracts.id', '=', 'clients.contract_id')
            ->select(
                'clients.id',
                'clients.name',
                'clients.email',
                'clients.document',
                'clients.person_type',
                'clients.start_date',
                'clients.contract_value',
                'clients.status',
                'contracts.name as contract_name'
            );

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('clients.name', 'like', "%{$search}%")
                    ->orWhere('clients.email', 'like', "%{$search}%")
                    ->orWhere('clients.document', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('clients.status', (int) $request->status);
        }

        if ($request->filled('person_type')) {
            $query->where('clients.person_type', $request->person_type);
        }

        if ($request->filled('contract_id')) {
            $query->where('clients.contract_id', $request->contract_id);
        }

        if ($request->filled('order_by') && $request->has('order.0.dir')) {
            $direction = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
            $column = match ($request->order_by) {
                'name' => 'clients.name',
                'contract' => 'contracts.name',
                'value' => 'clients.contract_value',
                'start_date' => 'clients.start_date',
                'status' => 'clients.status',
                default => 'clients.id',
            };

            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('clients.id', 'desc');
        }

        return DataTables::of($query)
            ->addColumn('name', function ($row) {
                return '<a href="' . route('clients.edit', $row->id) . '" class="text-gray-700 text-hover-primary fw-bold fs-6">' . e($row->name) . '</a>';
            })
            ->addColumn('contract', function ($row) {
                if (empty($row->contract_name)) {
                    return "<span class='badge badge-light'>-</span>";
                }

                return "<span class='text-gray-700 fw-bold fs-6'>" . e($row->contract_name) . "</span>";
            })
            ->addColumn('value', function ($row) {
                return $row->contract_value ?: '-';
            })
            ->addColumn('start_date', function ($row) {
                if (empty($row->start_date)) {
                    return "<span class='badge badge-light'>-</span>";
                }

                return "<span class='badge badge-light'>" . date('d/m/Y', strtotime($row->start_date)) . "</span>";
            })
            ->addColumn('status', function ($row) {
                if ((int) $row->status === 1) {
                    return "<span class='badge badge-light-success'>Ativo</span>";
                }

                return "<span class='badge badge-light-danger'>Inativo</span>";
            })
            ->addColumn('actions', function ($row) {
                $toggleIcon = (int) $row->status === 1
                    ? "<i class='fas fa-times-circle' title='Desativar'></i>"
                    : "<i class='fas fa-redo' title='Reativar'></i>";

                return "<div class='d-flex align-items-center icons-table'>
                            <a href='" . route('clients.edit', $row->id) . "'>
                                <i class='fas fa-edit' title='Editar'></i>
                            </a>
                            <a href='" . route('clients.destroy', $row->id) . "'>
                                {$toggleIcon}
                            </a>
                            <a href='#' class='js-delete-client' data-url='" . route('clients.delete', $row->id) . "' data-name='" . e($row->name) . "'>
                                <i class='fas fa-trash-alt text-hover-danger' title='Excluir'></i>
                            </a>
                        </div>";
            })
            ->rawColumns(['name', 'contract', 'start_date', 'status', 'actions'])
            ->toJson();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // GET ALL DATA
        $contracts = Contract::where('status', true)->orderBy('id', 'ASC')->get();

        // RENDER VIEW
        return view('pages.clients.create')->with([
            'contracts' => $contracts,
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

        // GET FORM DATA
        $data = $request->all();

        // Obtém projeto
        $data['created_by'] = Auth::id();

        // SEND DATA
        $this->repository->create($data);

        // REDIRECT AND MESSAGES
        return redirect()
                ->route('clients.index')
                ->with('message', 'Cliente adicionado com sucesso.');

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
        $contracts = Contract::where('status', true)->orderBy('id', 'ASC')->get();

        // VERIFY IF EXISTS
        if(!$content) return redirect()->back();

        // GENERATES DISPLAY WITH DATA
        return view('pages.clients.edit')->with([
            'content' => $content,
            'contracts' => $contracts,
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

        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id))
        return redirect()->back();

        // GET FORM DATA
        $data = $request->all();

        // UPDATE BY
        $data['updated_by'] = Auth::id();

        // STORING NEW DATA
        $content->update($data);

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('clients.edit', $content->id)
            ->with('message', 'Cliente atualizado com sucesso.');

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

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('clients.index')
            ->with('message', 'Cliente ' . ($status == false ? 'desativado' : 'habilitado') . ' com sucesso.');

    }

    /**
     * Remove permanently the specified resource from storage.
     */
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
