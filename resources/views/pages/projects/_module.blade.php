<div class="card mb-6">
    <div class="card-header min-h-50px ps-2 pe-6">
            <div class="d-flex align-items-center ps-3 pe-5 w-50">
                <input class="form-control form-control-flush bg-hover-light rounded py-2 px-6 text-gray-700 fs-5 fw-bold p-0" value="{{ $module->name }}">
            </div>
            <div class="d-none d-md-flex align-items-center">
                <div class="w-125px text-center text-gray-700 fs-7 text-uppercase fw-bold">
                    Designado
                </div>
                <div class="d-flex align-items-center justify-content-center cursor-pointer w-150px text-gray-700 fs-7 text-uppercase fw-bold">
                    Status
                </div>
                <div class="d-flex align-items-center justify-content-center cursor-pointer w-200px text-gray-700 fs-7 text-uppercase fw-bold">
                    Data
                </div>
                <div>
                    <i class="fa-solid fa-arrows-to-dot text-hover-primary cursor-pointer py-2 px-3 mx-3 fs-7 text-gray-700"></i>
                </div>
            </div>
    </div>
    <div class="card-body p-5">
        <div class="draggable-zone load-tasks-project" data-type="project" style="min-height: 50px;" data-project="{{ $module->id }}" id="project-tasks-{{ $module->id }}">
            @if ($module->tasks()->whereNull('task_id')->count())
                @foreach ($module->tasks()->where('status', 1)->whereNull('task_id')->where('checked', false)->orderBy('order', 'ASC')->orderBy('updated_at', 'DESC')->get() as $task)
                    @include('pages.tasks._tasks')
                @endforeach
            @endif
            <div class="no-tasks" @if ($module->tasks()->where('status', 1)->whereNull('task_id')->where('checked', false)->count()) style="display: none;" @endif>
                <div class="rounded bg-light d-flex align-items-center justify-content-center h-50px">
                    <div class="text-center">
                        <p class="m-0 text-gray-600 fw-bold text-uppercase">Sem tarefas ainda nesse projeto</p>
                    </div>
                </div>
            </div>
        </div>
        <form action="#" method="POST" class="send-tasks">
            @csrf
            <input type="hidden" name="module_id" value="{{ $module->id }}">
            <input type="text" name="name" class="form-control form-control-solid w-100 rounded mt-5" placeholder="Inserir nova tarefa" required>
        </form>
    </div>
</div>