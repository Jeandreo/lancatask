<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, Project $content)
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

        // GET ALL DATA
        $contents = $this->repository->orderBy('name', 'ASC')->get();

        // RETURN VIEW WITH DATA
        return view('pages.projects.index')->with([
            'contents' => $contents,
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function releases()
    {

        // GET ALL DATA
        $contents = $this->repository->where('status', true)->where('type_id', 1)->orderBy('name', 'ASC')->get();

        // RETURN VIEW WITH DATA
        return view('pages.projects.releases')->with([
            'contents' => $contents,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // RENDER VIEW
        $users = User::where('status', 1)->get();
        $types = ProjectType::where('status', 1)->get();
        return view('pages.projects.create')->with([
            'users' => $users,
            'types' => $types,
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

        // CREATED BY
        $data['created_by'] = Auth::id();

        // SEND DATA
        $created = $this->repository->create($data);

        // Sincroniza time
        $created->users()->sync($data['team']);

        // Cria módulo inicial
        $createdModule = Module::create([
            'name' => 'Primeiro Módulo de ' . $created['name'],
            'project_id' => $created->id,
            'color' => '#348feb',
            'created_by' => Auth::id(),
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $createdModule->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $createdModule->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $createdModule->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        // REDIRECT AND MESSAGES
        return redirect()
                ->route('projects.show', $created->id)
                ->with('message', 'Projeto adicionado com sucesso.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // Obtém projeto
        if(!$project = $this->repository->find($id)){
            return redirect()->route('projects.index');
        }

        // GET USERS
        $users = User::where('status', 1)->get();

        // RETURN VIEW WITH DATA
        return view('pages.projects.show')->with([
            'project' => $project,
            'users' => $users,
            'pageClean' => true,
        ]);
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
        $users = User::where('status', 1)->get();

        // VERIFY IF EXISTS
        if(!$content) return redirect()->back();

        // GENERATES DISPLAY WITH DATA
        return view('pages.projects.edit')->with([
            'content' => $content,
            'users' => $users,
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

        // Sincroniza time
        $content->users()->sync($data['team']);

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('projects.index')
            ->with('message', 'Projeto editado com sucesso.');

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
            ->route('projects.index')
            ->with('message', 'Projeto ' . ($status == false ? 'desativado' : 'habiliitado') . ' com sucesso.');

    }
}
