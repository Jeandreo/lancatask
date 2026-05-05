<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\AgendaMember;
use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // GET ALL DATA
        $contracts = Contract::where('status', true)->orderBy('id', 'ASC')->get();
        $projectTypes = ProjectType::where('status', true)->orderBy('name', 'ASC')->get();

        // RENDER VIEW
        return view('pages.clients.create')->with([
            'contracts' => $contracts,
            'projectTypes' => $projectTypes,
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
        $data = $request->all();
        $data['created_by'] = Auth::id();

        if ($request->boolean('create_project')) {
            $request->validate([
                'project_name' => 'required|string|max:255',
                'project_type_id' => 'required|exists:projects_types,id',
            ]);
        }

        DB::transaction(function () use ($data, $request) {
            $this->repository->create($data);

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
