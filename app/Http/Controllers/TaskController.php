<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
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
    public function index(Request $request)
    {

        // GET ALL DATA
        $contents = Project::orderBy('name', 'ASC');
        $contents = $request->project_id == 0 ? $contents : $contents->where('id', $request->project_id);
        $contents = $contents->get();

        $users = User::where('status', 1)->get();

        // RETURN VIEW WITH DATA
        return view('pages.tasks.index')->with([
            'contents' => $contents,
            'users' => $users,
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
    public function show(Request $request)
    {
        // GET ALL DATA
        $contents = $this->repository->find($request->task_id);

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

        // UPDATE BY
        $data['updated_by'] = Auth::id();

        // STORING NEW DATA
        $updated = $content->update($data);

        // SAVE AND RENAME IMAGE
        if($updated && $request->hasFile('image')){
            $request->file('image')->storeAs('public/images', $id . '.jpg');
        }

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('projects.index')
            ->with('message', 'Projeto editado com sucesso.');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAjax(Request $request, $id)
    {

        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id))
        return redirect()->back();

        // GET FORM DATA
        $data = $request->all();

        // UPDATE VALUE
        $data[$request->input] = $request->value;

        // UPDATE BY
        $data['updated_by'] = Auth::id();

        // STORING NEW DATA
        $content->update($data);

        // REDIRECT AND MESSAGES
        return response()->json('Success', 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function separator(Request $request, $id)
    {

        // GET BY POST
        if($id == null) $id = $request->task_id;

        // GET DATA
        $content = $this->repository->find($id);

        // UPDATE
        if($content->separator == 0){
            $status = 1;
        } else {
            $status = 0;
        }

        // STORING NEW DATA
        $this->repository->where('id', $id)->update(['separator' => $status, 'updated_by' => Auth::id()]);

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function standBy(Request $request, $id = null)
    {

        // GET BY POST
        if($id == null) $id = $request->task_id;

        // GET DATA
        $content = $this->repository->find($id);

        // UPDATE
        if($content->status == 2){
            $status = 1;
            $message = 'Tarefa ativada! Bora pra cima!!! 💪🏼';
        } else {
            $status = 2;
            $message = 'Tarefa em stand-by.';
        }

        // STORING NEW DATA
        $this->repository->where('id', $id)->update(['status' => $status, 'updated_by' => Auth::id()]);

        // REDIRECT AND MESSAGES
        return redirect()
                ->back()
                ->with('message', $message);

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
        $check = $contents->checked == true ? false : true;

        // SAVE
        $contents->checked = $check;
        $contents->checked_at = now();
        $contents->save();

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

        // MARK AS CHECK
        $contents->designated_id = $request->designated_id;
        $contents->save();

        // GET IMAGE
        $img = findImage('users/' . $request->designated_id . '/' . 'perfil-35px.jpg');

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
        $content->status_id = $request->status_id;
        $content->save();

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
        $content->date = $request->date;
        $content->save();

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
            $startProject = $task->project_id;
            $task->project_id = $request->project_id;
            $task->save();
            $return['startProject'] = $startProject;
        }


        if($request->project_id){
            // PROJECT
            $project = Project::find($request->project_id);
            $return['color'] = $project->color;
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
    public function others($type)
    {

        // FILTER TYPE
        $filterStatus = $type == 'ideias' ? 2 : 0;

        // GET ALL DATA
        $contents = $this->repository->where('status', $filterStatus)->get();
        $users = User::where('status', 1)->get();

        // RETURN VIEW WITH DATA
        return view('pages.tasks.others')->with([
            'contents' => $contents,
            'users' => $users,
            'type' => $type,
        ]);
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
}
