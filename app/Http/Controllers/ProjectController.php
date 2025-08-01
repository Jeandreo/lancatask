<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $contents = $this->repository->orderBy('id', 'ASC')->get();

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
        $contents = $this->repository->where('status', true)->where('type_id', 1)->orderBy('id', 'ASC')->get();

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
        if($data['type_is'] == 'time'){
            $created->users()->sync($data['team']);
        } else {
            $created->users()->sync(Auth::id());
        }

        // Cria módulo inicial
        Module::create([
            'project_id' => $created->id,
            'color' => '#348feb',
            'created_by' => Auth::id(),
        ]);

        Status::create([
            'name' => 'Não Iniciado',
            'color' => '#365e92',
            'project_id' => $created->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Parado',
            'color' => '#D83F58',
            'project_id' => $created->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#F4A541',
            'project_id' => $created->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Feito',
            'color' => '#63BC07',
            'project_id' => $created->id,
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

        // Verifica se o usuário autenticado está no projeto
        if (!$project->users->contains(Auth::user()) && $project->created_by != Auth::id()) {
            // Redireciona para a página inicial ou exibe um erro
            return redirect()->route('projects.index')->with('message', 'Você não tem permissão para acessar este projeto.');
        }

        // GET USERS
        $users = User::where('status', 1)->get();

        // RETURN VIEW WITH DATA
        return view('pages.projects.show')->with([
            'project' => $project,
            'users' => $users,
            // 'pageClean' => true,
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
        $types = ProjectType::where('status', 1)->get();

        // VERIFY IF EXISTS
        if(!$content) return redirect()->back();

        // GENERATES DISPLAY WITH DATA
        return view('pages.projects.edit')->with([
            'content' => $content,
            'users' => $users,
            'types' => $types,
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
        if($data['type_is'] == 'time'){
            $content->users()->sync($data['team']);
        } else {
            $content->users()->sync(Auth::id());
        }

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
            ->with('message', 'Projeto ' . ($status == false ? 'desativado' : 'habilitado') . ' com sucesso.');

    }

    /**
     * Duplicates the specified project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate($id)
    {
        // Obter o projeto original
        $project = Project::with(['modules.tasks', 'statuses'])->find($id);

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Projeto não encontrado.');
        }

        // Iniciar transação para garantir consistência
        DB::beginTransaction();

        try {
            // Duplicar o projeto
            $newProject = $project->replicate();
            $newProject->name .= ' (duplicado)';
            $newProject->created_by = Auth::id();
            $newProject->save();

            // Duplicar os statuses e obter o primeiro
            foreach ($project->statuses as $index => $status) {
                $newStatus = $status->replicate();
                $newStatus->project_id = $newProject->id;
                $newStatus->created_by = Auth::id();
                $newStatus->save();

                if ($index === 0) {
                    $firstStatusId = $newStatus->id;
                }
            }

            // Duplicar os módulos e tarefas
            foreach ($project->modules as $module) {
                $newModule = $module->replicate();
                $newModule->project_id = $newProject->id;
                $newModule->created_by = Auth::id();
                $newModule->save();

                foreach ($module->tasks as $task) {
                    $newTask = $task->replicate();
                    $newTask->module_id = $newModule->id;
                    $newTask->status_id = $firstStatusId;
                    $newTask->date = null;
                    $newTask->date_start = null;
                    $newTask->date_end = null;
                    $newTask->created_by = Auth::id();
                    $newTask->save();
                }
            }

            // Finalizar transação
            DB::commit();

            return redirect()
                ->route('projects.index')
                ->with('message', 'Projeto duplicado com sucesso.');
        } catch (\Exception $e) {
            // Reverter transação em caso de erro
            DB::rollBack();

            return redirect()
                ->route('projects.index')
                ->with('error', 'Erro ao duplicar o projeto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        // GET DATA
        $project = $this->repository->find($id);

        // Apagar tarefas relacionadas aos módulos do projeto
        foreach ($project->modules as $module) {
            // Excluir registros dependentes (tasks_participants) antes de excluir as tarefas
            foreach ($module->tasks as $task) {
                // Apagar participantes das tarefas
                $task->participants()->detach();
            }

            // Excluir tarefas do módulo
            $module->tasks()->delete();

            // Excluir o módulo
            $module->delete();
        }

        // Apagar statuses do projeto
        $project->statuses()->delete();

        // Desvincular usuários do projeto
        $project->users()->detach();

        // Apagar o próprio projeto
        $project->delete();

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('projects.index')
            ->with('message', 'Projeto apagado com sucesso.');
    }

}
