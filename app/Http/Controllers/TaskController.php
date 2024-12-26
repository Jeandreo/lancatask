<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskHistoric;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // RETURN VIEW WITH DATA
        return view('pages.tasks.index')->with([
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
        $data['created_by'] = $data['designated_id'] = Auth::id();

        // Obtém Módulo
        $module = Module::find($data['module_id']);
        $data['status_id'] = $module->project->statuses()->first()->id ?? 1;

        // SEND DATA
        $created = $this->repository->create($data);

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
        $contents = $this->repository->find($id);

        // RETURN VIEW WITH DATA
        return view('pages.tasks.show')->with([
            'contents' => $contents,
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
    public function designated(Request $request)
    {

        // GET ALL DATA
        $contents = Task::find($request->task_id);
        $previousValue = $contents->designated_id;
        $contents->designated_id = $request->designated_id;
        $contents->save();

        TaskHistoric::create([
            'task_id'      => $request->task_id,
            'action'       => 'designado',
            'previous_key' => $previousValue,
            'key'          => $request->designated_id,
            'created_by'   => Auth::id(),
        ]);

        // GET IMAGE
        $img = findImage('users/photos/' . $request->designated_id . '.jpg');

        // RETURN
        return response()->json($img, 200);

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
        $content->date = $request->date;
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
    public function subtask(Request $request)
    {

        // GET TASK
        $created = Task::create([
            'task_id' => $request->task_id,
            'project_id' => $request->project_id,
            'designated_id' => Auth::id(),
            'created_by' => Auth::id(),
        ]);

        // GET USERS
        $users = User::where('status', 1)->get();
        $task = Task::find($created->id);

        // RETURN VIEW WITH DATA
        return view('pages.tasks._subtask')->with([
            'subtask' => $task,
            'users' => $users,
        ]);

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
