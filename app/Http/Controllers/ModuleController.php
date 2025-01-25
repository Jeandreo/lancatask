<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleOrder;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, Module $content)
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
        $contents = $this->repository->orderBy('id', 'ASC')->get();

        // RETURN VIEW WITH DATA
        return view('pages.modules.index')->with([
            'contents' => $contents,
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
        $data['color'] = randomColor();
        $data['created_by'] = Auth::id();

        // SEND DATA
        $module = $this->repository->create($data);

        // Renderiza modulo
        $moduleHtml = view('pages.projects._module')->with([
            'module' => $module,
        ])->render();

        // REDIRECT AND MESSAGES
        return response()->json([
            'message' => 'Modulo criado com sucesso',
            'html' => $moduleHtml,
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

        // VERIFY IF EXISTS
        if(!$content) return redirect()->back();

        // GENERATES DISPLAY WITH DATA
        return view('pages.projects.edit')->with([
            'content' => $content,
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
        return response()->json();

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

        if($status == true){
            // REDIRECT AND MESSAGES
            return redirect()
                    ->route('projects.show', $content->project_id)
                    ->with('message', 'Módulo reativado com sucesso.');
        } else {
            // REDIRECT AND MESSAGES
            return response()->json();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request, $id)
    {
        // GET ALL DATA
        $content = Project::find($id);

        // Obtém dados
        $data = $request->all();

        // VERIFY IF EXISTS
        if(!$content) return redirect()->back();

        // Obtém módulos
        $modules = $content->modules;

        // Busca tarefas
        $newModules = [];

        // Loop
        foreach ($modules as $module) {

            // Busca tarefas
            $tasks = Task::where('module_id', $module->id)->where('status', 1);

            // Filtra participantes
            if (isset($data['users'])) {
                $tasks = $tasks->whereHas('participants', function ($query) use ($data) {
                    $query->whereIn('users.id', $data['users']);
                });
            }

            // Filtra pelo nome
            if($data['name']){
                $tasks = $tasks->where('name', 'LIKE', '%' . $data['name'] . '%');
            }

            // Filtra status
            if(isset($data['status'])){
                $tasks = $tasks->whereIn('status_id', $data['status']);
            }

            // Ordena
            $tasks = $tasks->orderBy('order', 'ASC')->orderBy('updated_at', 'DESC')->get();

            // Modulo e tareafas
            $taskSection['id']   = $module->id;
            $taskSection['html'] = view('pages.projects._module_zone')->with([
                'tasks' => $tasks,
            ])->render();
            $newModules[] = $taskSection;
        }

        return $newModules;

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request, $id)
    {

        // GET FORM DATA
        $data = $request->all();

        // Apaga a ordem atual
        ModuleOrder::where('user_id', Auth::id())->where('project_id', $id)->delete();

        // Cria nova ordenação
        $count = 1;
        foreach ($data['modulesOrder'] as $moduleId) {
            // STORING NEW DATA
            ModuleOrder::create([
                'order' =>  $count,
                'user_id' => Auth::id(),
                'module_id' => $moduleId,
                'project_id' => $id,
            ]);
            ++$count;
        }

        // REDIRECT AND MESSAGES
        return response()->json();

    }
}
