<div class="border-top-tasks">
    <div class="bg-dark rounded p-0 d-flex align-items-center justify-content-between mb-0 shadow-list dmk-tasks h-35px task-list task-on-subtask " data-task="{{ $task->id }}">
        <div class="d-flex align-items-center justify-content-between w-100 h-100">
            <div class="d-flex align-items-center h-100 w-100">
                <div style="background: {{ $task->checked == false ? $task->module->color : '#d5d5d5' }};" class="rounded-start h-100 d-flex align-items-center color-task task-icons overflow-hidden task-module-{{ $task->module_id }}">
                    <div class="form-check form-check-custom form-check-solid py-2 ps-5 me-5">
                        <input class="form-check-input w-15px h-15px cursor-pointer check-task task-main" data-task="{{ $task->id }}" type="checkbox" value="1" style="border-radius: 3px" @if($task->checked == true) checked @endif/>
                        <span class="show-task" data-task="{{ !$task->task_id ? $task->id : $task->task_id }}">
                            <i class="fa-solid fa-eye p-1 fs-5 text-white ms-5 cursor-pointer zoom-hover zoom-hover-03"></i>
                        </span>
                        <span class="tasks-destroy" data-task="{{ $task->id }}">
                            <i class="fa-solid p-1 fa-trash-alt fs-5 text-white ms-3 cursor-pointer zoom-hover zoom-hover-03"></i>
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center h-100 w-100 div-name-task z-index-9">
                    <div class="d-block w-100 px-3 px-md-0 ms-5">
                        <p class="text-gray-600 text-hover-primary fs-5 lh-1 fw-normal p-0 m-0 border-0 w-100 cursor-pointer show-task py-3" data-task="{{ $task->id }}" id="rename-task-{{ $task->id }}">
                            @if (isset($showProject))
                            <span class="fw-bold text-gray-600 fs-6">{{ $task->module->project->name }} - </span> @if($task->module->name) <span class="fw-bold text-gray-600 fs-6">{{ $task->module->name }} - </span> @endif
                            @endif
                            {{ $task->name }}
                        </p>
                    </div>
                </div>
            </div>
            @if ($task->comments->count())
            <span>
                <i class="fa-regular fa-comments text-gray-700 p-2 ms-5"></i>
            </span>
            @endif
            @if ($task->subtasks->where('status', true)->count())
            <i class="fa-solid fa-angle-right p-2 cursor-pointer text-gray-700 show-subtasks rotate @if($task->open_subtasks) rotate-90 @endif" data-task="{{ $task->id }}"></i>
            @endif
            @if ($task->task_id)
            <i class="fa-solid fa-diagram-predecessor pe-2 text-gray-700" data-bs-toggle="tooltip" title="{{ $task->father->name }}"></i>
            @endif
            <span class="task-priority d-none d-md-flex" data-task="{{ $task->id }}">
            <i class="fa-solid fa-font-awesome p-2
                @if ($task->priority == 0)
                text-gray-700
                @elseif($task->priority == 1)
                text-warning
                @elseif($task->priority == 2)
                text-info
                @elseif($task->priority == 3)
                text-danger
                @endif
                cursor-pointer me-5"></i>
            </span>
        </div>
        <div class="d-flex align-items-center h-100 d-none d-md-flex">
            <div class="separator-vertical h-100"></div>
            <div class="w-125px symbol-group symbol-hover flex-nowrap justify-content-center list-participants-{{ $task->id }} opacity-1">
                @include('pages.tasks._participants', ['opacity0' => true])
            </div>
            <div class="task-check h-100" style="@if(!$task->checked) display: none; @endif">
                <div class="d-flex p-0 align-items-center justify-content-center cursor-pointer h-100 w-150px rounded-0 done-status" style="background: #44bd00;">
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                        <p class="text-white fw-bold m-0 text-center status-name">Concluído</p>
                    </div>
                </div>
            </div>
            <div class="task-no-check h-100" style="@if($task->checked) display: none; @endif">
                <div class="d-flex p-0 align-items-center justify-content-center cursor-pointer h-100 w-150px rounded-0 actual-status" style="background: {{ $task->statusProject->color }};">
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-start">
                        <p class="text-white fw-bold m-0 text-center status-name">{{ $task->statusProject->name }}</p>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-250px py-4" data-kt-menu="true">
                            @foreach ($task->module->project->statuses->where('status', true) as $status)
                            <div class="menu-item px-3 mb-2">
                                <span data-task="{{ $task->id }}" data-status="{{ $status->id }}" class="menu-link px-3 d-block text-center tasks-status" style="background: {{ $status->color }}; color: white">
                                    {{ $status->name }}
                                </span>
                            </div>
                            @endforeach
                            <a href="{{ route('projects.edit', $task->module->project_id) }}" class="px-3 d-block text-center text-gray-600 text-hover-primary fw-semibold fs-8 text-uppercase redirect-this">
                                Gerenciar Status
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="position-relative opacity-1">
                <input type="text"
                    class="form-control border-0 form-control-sm flatpickr w-auto text-center w-200px task-date task-date-{{ $task->id }}
                    @if(date('Y-m-d', strtotime($task->date_end)) == date('Y-m-d') || $task->checked)
                        text-success
                    @elseif(strtotime($task->date_end) < time())
                        text-danger
                    @elseif(-(\Carbon\Carbon::parse($task->date_end)->diffInDays()) <= 2)
                        text-primary
                    @else
                        text-gray-700
                    @endif"
                    data-task="{{ $task->id }}"
                    placeholder="Prazo da tarefa"
                    value="@if(isset($task->date_start) && $task->date_start == $task->date_end){{ date('d/m/Y', strtotime($task->date_start)) }}@elseif(isset($task->date_start)){{ date('d/m', strtotime($task->date_start)) }} até {{ date('d/m', strtotime($task->date_end)) }}@endif"/>
                {{-- <i class="fa-solid fa-calendar-xmark text-hover-primary text-gray-700 py-2 px-3 fs-7 position-absolute opacity-0 cursor-pointer remove-date" data-task="{{ $task->id }}" style="top: 15%; right: 0"></i> --}}
            </div>
            @if (!isset($hideMove))
            <div class="separator-vertical h-100"></div>
            <div>
                <i class="fa-solid fa-arrows-to-dot text-hover-primary py-2 px-3 mx-3 fs-6 draggable-handle"></i>
            </div>
            @endif
        </div>
    </div>
</div>
