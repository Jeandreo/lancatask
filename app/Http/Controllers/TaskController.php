<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskHistoric;
use App\Models\TaskParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, Task $content)
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
        $projects = Project::where('status', 1)->get();
        $modules = Module::where('status', 1)->get()->groupBy('name');
        $status = Status::where('status', 1)->get()->groupBy('name');

        $modulesFormated = [];
        foreach ($modules as $key => $value) {
            $newModule['name'] = $key;
            $newModule['values'] = implode(',', $value->pluck('id')->toArray());
            $modulesFormated[] = $newModule;
        }

        $statusFormated = [];
        foreach ($status as $key => $value) {
            $newStatus['name'] = $key;
            $newStatus['values'] = implode(',', $value->pluck('id')->toArray());
            $statusFormated[] = $newStatus;
        }

        // RETURN VIEW WITH DATA
        return view('pages.tasks.index')->with([
            'contents' => $contents,
            'projects' => $projects,
            'modules' => $modulesFormated,
            'status' => $statusFormated,
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function processing(Request $request)
    {

        // Obtém dados
        $data = $request->all();

        // Inicia consulta
        $query = DB::table('tasks')
        ->leftJoin('modules', 'tasks.module_id', '=', 'modules.id')
        ->leftJoin('statuses', 'tasks.status_id', '=', 'statuses.id')
        ->leftJoin('projects', 'modules.project_id', '=', 'projects.id');

        // SEARCH BY
        if ($data['searchBy'] != '') {
                $query->where('tasks.name', 'like', "%{$data['searchBy']}%");
        }

        // Ordena
        if ($data['order']) {

            // ORDER & COLUMN
            $direction = $request->order[0]['dir'];
            $orderThis = $request->order_by;
            $column = $orderThis;

            // FORMATA COLUNAS
            switch ($column) {
                case 'id':
                    $column = 'tasks.id';
                    break;
                case 'name':
                    $column = 'tasks.name';
                    break;
                case 'when':
                    $column = 'tasks.date_start';
                    break;
                case 'checked':
                    $column = 'tasks.checked';
                    break;
                case 'project':
                    $column = 'projects.name';
                    break;
                case 'module':
                    $column = 'modules.name';
                    break;
                case 'status':
                    $column = 'statuses.name';
                    break;
                default:
                    $column = 'tasks.id';
                    break;
            }
            $query->orderBy($column, $direction);

        }

        // Filtra Status
        if (isset($data['projects'])) {
            $query->whereIn('modules.project_id', $data['projects']);
        }

        // Filtra Status
        if(isset($data['modules'])){
            $query->whereIn('module_id', $data['modules']);
        }
        // Filtra Status
        if(isset($data['status'])){
            $query->whereIn('status_id', $data['status']);
        }

        // Se quiser filtrar por data de registro
        if(isset($data['register'])){

            // Extrai a data
            $dates = explode(" - ", $data['register']);

            // Formata
            $dateFormated[0] = convertDateFormat($dates[0]);
            $dateFormated[1] = convertDateFormat($dates[1]);

            // Incluí na consulta
            $query->whereBetween('tasks.date_start', $dateFormated);

        }

        // Add the necessary columns to the GROUP BY clause
        $query->groupBy(
            'tasks.id',
            'tasks.name',
            'tasks.date_start',
            'tasks.date_end',
            'tasks.checked',
            'tasks.status',
            'modules.name',
            'modules.color',
            'projects.name',
            'statuses.name',
            'statuses.color',
        );

        // COUNT TOTAL RECORDS
        $totalRecords = $query->select('tasks.id')->count();

        // ITENS PER PAGE AND PAGINATE
        $pages = $query->paginate($request->per_page);

        // Seleciona os dados
        $query->select(
            'tasks.id as id',
            'tasks.name as name',
            'tasks.date_start as date_start',
            'tasks.date_end as date_end',
            'tasks.checked as checked',
            'tasks.status as status',
            'modules.name as module_name',
            'modules.color as module_color',
            'projects.name as project_name',
            'statuses.name as status_name',
            'statuses.color as status_color',
        );

        return DataTables::of($query)
            ->addColumn('id', function ($row) {
                $html =
                '<span class="text-gray-700 text-hover-primary fw-bold fs-6 show-task cursor-pointer" data-task="' . $row->id .  '">
                    ' . $row->id .  '
                </span>';
                return $html;
            })
            ->addColumn('name', function ($row) {
                $html =
                '<span class="text-gray-700 text-hover-primary fw-bold fs-6 show-task cursor-pointer" data-task="' . $row->id .  '">
                    ' . $row->name .  '
                </span>';
                return $html;
            })
            ->addColumn('when', function ($row) {
                if(!$row->date_start){
                    $html = '<span class="text-gray-600">Sem data</span>';
                }else if ($row->date_start == $row->date_end){
                    $html = '<span class="text-gray-600">' . date('d/m/Y', strtotime($row->date_start)) .  '</span>';
                } else {
                    $html = '<span class="text-gray-600">' . date('d/m/Y', strtotime($row->date_start)) . ' até ' . date('d/m/Y', strtotime($row->date_end)) .  '</span>';
                }
                return $html;
            })
            ->addColumn('checked', function ($row) {
                if ($row->checked == true){
                    $html = '<span class="badge badge-light-success">Concluída</span>';
                } else {
                    $html = '<span class="badge badge-light-danger">Não concluída</span>';
                }
                return $html;
            })
            ->addColumn('project', function ($row) {
                return '<span class="fw-bold text-gray-700">'.$row->project_name.'</span>';
            })
            ->addColumn('module', function ($row) {
                return '<span class="badge" style="background: '.hex2rgb($row->module_color, 5).'; color: '.$row->module_color.'">'.$row->module_name.'</span>';
            })
            ->addColumn('status', function ($row) {
                return '<span class="badge" style="background: '.hex2rgb($row->status_color, 12).'; color: '.$row->status_color.'">'.$row->status_name.'</span>';
            })
            ->addColumn('actions', function ($row) {
                if ($row->status == 1){
                    $btnToogle = '<i class="fas fa-times-circle" title="Desativar"></i>';
                } else {
                    $btnToogle = '<i class="fas fa-redo" title="Reativar"></i>';
                }
                $html = '<div class="d-flex align-items-center icons-table">
                            <a href="' . route('tasks.destroy', $row->id) . '">
                                ' . $btnToogle . '
                            </a>
                        </div>';
                return $html;
            })
            ->rawColumns(['id', 'name', 'when', 'checked', 'project', 'module', 'status', 'actions'])
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($pages->total())
            ->toJson();
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

        // Obtém Módulo
        $module = Module::find($data['module_id']);
        $data['status_id'] = $module->project->statuses()->first()->id ?? 1;
        $data['date'] = $data['date_start'] = $data['date_end'] = now();

        // SEND DATA
        $created = $this->repository->create($data);

        // Adiciona participante
        TaskParticipant::create([
            'user_id' => $data['created_by'],
            'task_id' => $created->id,
        ]);

        // REDIRECT AND MESSAGES
        return response()->json($created->toArray(), 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // GET ALL DATA
        $task = $this->repository->find($id);

        // RETURN VIEW WITH DATA
        return view('pages.tasks.show')->with([
            'task' => $task,
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
        return view('pages.tasks.edit')->with([
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

        // Input
        $input = $request->input;

        // UPDATE VALUE
        $data[$request->input] = $request->value;

        // Valor antigo
        $previousValue = $content->$input;

        // UPDATE BY
        $data['updated_by'] = Auth::id();

        // STORING NEW DATA
        $content->update($data);

        // Cria histórico
        if($data['input'] == 'name'){
            TaskHistoric::create([
                'task_id'      => $id,
                'action'       => 'nome',
                'previous_key' => $previousValue,
                'key'          => $request->value,
                'created_by'   => Auth::id(),
            ]);
        } elseif ($data['input'] == 'description') {
            TaskHistoric::create([
                'task_id'      => $id,
                'action'       => 'descrição',
                'previous_key' => $previousValue,
                'key'          => $request->value,
                'created_by'   => Auth::id(),
            ]);
        }

        // REDIRECT AND MESSAGES
        return response()->json('Success', 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = null)
    {

        // GET BY POST
        if($id == null) $id = $request->task_id;

        // GET DATA
        $content = $this->repository->find($id);

        // UPDATE
        if($content->status == 1){
            $status = 0;
            $message = 'Tarefa removida.';
        } else {
            $status = 1;
            $message = 'Tarefa ativada! Bora pra cima!!! 💪🏼';
        }

        TaskHistoric::create([
            'task_id'      => $request->task_id ?? $id,
            'action'       => 'estado',
            'key'          => $status,
            'created_by'   => Auth::id(),
        ]);

        // STORING NEW DATA
        $this->repository->where('id', $id)->update(['status' => $status, 'updated_by' => Auth::id()]);

        // REDIRECT AND MESSAGES
        return redirect()
                ->back()
                ->with('message', $message);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAll($id)
    {

        // Obtém projeto
        $project = Project::find($id);

        // Módulos
        $modules = $project->modules()->pluck('id')->toArray();
        
        // STORING NEW DATA
        $this->repository->where('checked', true)->where('module_id', $modules)->update(['status' => false, 'updated_by' => Auth::id()]);

        // REDIRECT AND MESSAGES
        return redirect()
                ->back()
                ->with('message', 'Tarefas Arquivadas');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showSubtasks(Request $request)
    {

        // GET DATA
        $content = $this->repository->find($request->task_id);

        // UPDATE
        if($content->open_subtasks == 1){
            $show = 0;
        } else {
            $show = 1;
        }

        // STORING NEW DATA
        $this->repository->where('id', $request->task_id)->update(['open_subtasks' => $show, 'updated_by' => Auth::id()]);

        // REDIRECT AND MESSAGES
        return response()->json('Success', 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajax($id)
    {

        // GET ALL DATA
        $tasks = Task::where('project_id', $id)->whereNull('task_id')->where('status', 1)->where('checked', 0)->orderBy('order', 'ASC')->orderBy('updated_at', 'DESC')->get();
        $users = User::where('status', 1)->get();

        // RETURN VIEW WITH DATA
        return view('pages.tasks._tasks')->with([
            'tasks' => $tasks,
            'users' => $users,
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {

        // GET ALL DATA
        $contents = Task::find($request->task_id);

        // MARK AS CHECK
        if($contents->checked == true){
            $check = false;
            $color = $contents->module->color;
        } else {
            $check = true;
            $color = '#d5d5d5';
        }

        // SAVE
        $contents->checked = $check;
        $contents->checked_at = now();
        $contents->save();

        //
        return response()->json($color, 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function priority(Request $request)
    {

        // GET ALL DATA
        $contents = Task::find($request->task_id);

        // MARK AS CHECK
        $priority = $contents->priority;

        // SET PRIORITY
        if($priority <= 2){
            $newPriority = $priority + 1;
        } else {
            $newPriority = 0;
        }

        // SAVE
        $contents->priority = $newPriority;
        $contents->save();

        TaskHistoric::create([
            'task_id'      => $request->task_id,
            'action'       => 'prioridade',
            'previous_key' => $priority,
            'key'          => $newPriority,
            'created_by'   => Auth::id(),
        ]);

        return response()->json($contents->priority, 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {

        // UPDATE TASK STATUS
        $content = Task::find($request->task_id);
        $previousValue = $content->status_id;
        $content->status_id = $request->status_id;
        $content->save();

        TaskHistoric::create([
            'task_id'      => $request->task_id,
            'action'       => 'status',
            'previous_key' => $previousValue,
            'key'          => $request->status_id,
            'created_by'   => Auth::id(),
        ]);

        // STATUS
        $status = Status::find($request->status_id);

        // RETURN
        return response()->json([
            "name" => $status->name,
            "color" => $status->color,
            "done" => $status->done,
        ], 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function date(Request $request)
    {

        // UPDATE TASK STATUS
        $content = Task::find($request->task_id);
        $previousValue = $content->date;
        $content->date_start = $request->date_start;
        $content->date_end = $request->date_end;
        $content->save();

        TaskHistoric::create([
            'task_id'      => $request->task_id,
            'action'       => 'data',
            'previous_key' => $previousValue,
            'key'          => $request->date,
            'created_by'   => Auth::id(),
        ]);

        // RETURN
        return response()->json('Sucesso', 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request)
    {

        // START POSITION 0
        $position = 0;

        // RETURN
        $return = [];

        if($request->task_id){
            // GET TASK
            $task = Task::find($request->task_id);
            $startModule = $task->module_id;
            $task->module_id = $request->module_id;
            $task->save();
            $return['startModule'] = $startModule;
        }


        if($request->module_id){
            // PROJECT
            $module = Module::find($request->module_id);
            $return['color'] = $module->color;
        }

        // SAVE NEW ORDER
        foreach($request->tasksOrderIds as $id){
            // STORING NEW DATA
            $content = Task::find($id);
            $content->order = $position;
            $content->save();

            // SAVE NEXT POSITION
            ++$position;
        }

        // RETURN
        return response()->json($return, 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkeds(Request $request)
    {

        // GET ALL DATA
        $contents = Task::where('project_id', $request->project_id)->where('checked', true)->get();

        // RETURN VIEW WITH DATA
        return view('pages.tasks._checkeds')->with([
            'contents' => $contents,
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function challenge(Request $request)
    {

        $task = Task::find($request->task_id);
        $task->challenge = $request->checked == 'true' ? true : false;
        $task->save();

        return response()->json($request->all(), 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function time(Request $request)
    {

        $task = Task::find($request->task_id);
        $task->date = null;
        $task->save();

        return response()->json($request->all(), 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showOne($id)
    {
        // GET ALL DATA
        $contents = $this->repository->find($id);
        $users = User::where('status', 1)->get();
        $projects = Project::where('status', 1)->get();

        // RETURN VIEW WITH DATA
        return view('pages.tasks._tasks')->with([
            'task' => $contents,
            'users' => $users,
            'projects' => $projects,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function participants($id)
    {

        // GET ALL DATA
        $task = $this->repository->find($id);

        // RETURN VIEW WITH DATA
        return view('pages.tasks._participants')->with([
            'task' => $task,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addParticipants($id)
    {

        // GET ALL DATA
        $contents = $this->repository->find($id);

        
        // Obtém projeto da tarefa
        $users = $contents->module->project->users;

        // RETURN VIEW WITH DATA
        return view('pages.tasks._add_participants')->with([
            'contents' => $contents,
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
    public function addParticipant(Request $request, $id)
    {
        // Verifica se o participante já está na tarefa
        $existingParticipant = TaskParticipant::where('user_id', $request->user_id)
            ->where('task_id', $id)
            ->first();

        if ($existingParticipant) {
            // Remove o participante existente
            $existingParticipant->delete();
            return response()->json(['message' => 'Participante removido da tarefa.']);
        } else {
            // Adiciona o participante à tarefa
            TaskParticipant::create([
                'user_id' => $request->user_id,
                'task_id' => $id,
            ]);
            return response()->json(['message' => 'Participante adicionado à tarefa.']);
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function historic($id)
    {

        // GET ALL DATA
        $contents = $this->repository->find($id);

        // RETURN VIEW WITH DATA
        return view('pages.tasks._historic')->with([
            'contents' => $contents,
        ]);

    }
}
