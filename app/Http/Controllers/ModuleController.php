<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use App\Models\Status;
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

        // Obtém projeto
        $project = Project::find($data['project_id']);

        // CREATED BY
        $data['name'] = 'Novo Módulo em ' . $project->name . ' #' . str_pad(($project->modules()->count() + 1), 3, '0', STR_PAD_LEFT);
        $data['color'] = randomColor();
        $data['created_by'] = Auth::id();

        // SEND DATA
        $module = $this->repository->create($data);

        // Insere Status
        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module->id,
            'order' => 1,
            'created_by' => 1,
        ]);

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
}
