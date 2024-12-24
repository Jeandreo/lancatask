<h2 class="text-gray-700 text-center fs-5 text-uppercase cursor-pointer show-tasks-fileds mb-4">CONCLUÍDAS</h2>
@foreach ($contents as $content)
    <div class="d-grid">
        <!-- BEGIN:TASK -->
        <div class="bg-white rounded p-0 d-flex align-items-center justify-content-between mb-2 shadow-list dmk-tasks h-45px task-list task-on-subtask z-index-1" data-task="{{ $content->id }}">
            <div class="d-flex align-items-center justify-content-between w-100 h-100">
                <div class="d-flex align-items-center h-100 w-100">
                    <div style="background: {{ $content->project->color }};" class="rounded-start h-100 d-flex align-items-center color-task ">
                        <div class="form-check form-check-custom form-check-solid py-2 px-5">
                            <input class="form-check-input w-15px h-15px cursor-pointer check-task task-main" data-task="{{ $content->id }}" disabled type="checkbox" value="1" style="border-radius: 3px" @if($content->checked == true) checked @endif/>
                        </div>
                    </div>
                    <div class="d-flex align-items-center h-100 w-100 div-name-task z-index-9">
                    <div class="w-30px"></div>
                    <div class="d-block min-w-300px w-100">
                        <p class="text-gray-600 text-hover-primary fs-6 lh-1 fw-normal p-0 mb-1 cursor-pointer border-0 w-100 task-name show-task" style="margin-top: 3px;" data-task="{{ $content->id }}">{{ $content->name }}</p>
                        <div class="input-phrase" @if($content->phrase == '') style="display: none;" @endif>
                            <input type="text" disabled class="text-gray-500 fs-6 lh-1 fw-normal p-0 m-0 border-0 w-100 fs-7 d-flex task-phrase z-index-9 h-15px mt-n1" maxlength="255" name="phrase" value="{{ $content->phrase }}" @if($content->phrase == '') style="border-bottom: dashed 1px #bbbdcb63 !important;" @endif data-task="{{ $content->id }}">
                        </div>
                    </div>
                    </div>
                </div>
                <span>
                <i class="fa-solid fa-font-awesome p-2 
                    @if ($content->priority == 0)
                    text-gray-300
                    @elseif($content->priority == 1)
                    text-warning
                    @elseif($content->priority == 2)
                    text-info
                    @elseif($content->priority == 3)
                    text-danger
                    @endif
                     me-5"></i>
                </span>
            </div>
            <div class="d-flex align-items-center h-100">
                <!-- SEPARATOR -->
                <div class="separator-vertical h-100"></div>
                <!-- SEPARATOR -->
                <div class="w-125px text-center designated-div">
                    <div class="symbol symbol-30px symbol-circle" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-start">
                    <div class="symbol symbol-25px symbol-circle me-2">
                            <img alt="Pic" src="{{ findImage('users/' . $content->designated_id . '/' . 'perfil-35px.jpg') }}" class="designated">
                    </div>
                    </div>
                </div>
                <div class="d-flex p-0 align-items-center justify-content-center h-100 w-150px rounded-0 actual-status" style="background: {{ $content->statusInfo->color }}">
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-start">
                    <p class="text-white fw-bold m-0 status-name">{{ $content->statusInfo->name }}</p>
                    </div>
                </div>
                <input type="text" class="form-control border-0 form-control-sm w-auto text-center w-200px bg-white task-date
                @if(date('Y-m-d', strtotime($content->date)) == date('Y-m-d'))
                    text-success
                @elseif(strtotime($content->date) < time())
                    text-danger
                @elseif(\Carbon\Carbon::parse($content->date)->diffInDays() <= 2)
                    text-primary
                @else
                    text-gray-700
                @endif" disabled placeholder="Prazo da tarefa" value="@if($content->date) {{ date('d/m/Y', strtotime($content->date)) }} @endif"/>
                <!-- SEPARATOR -->
                <div class="separator-vertical h-100"></div>
                <!-- SEPARATOR -->
                <div>
                    @if ($content->status == 0)
                    <i class="fa-solid fa-trash-alt py-2 px-3 mx-3 fs-6 draggable-handle"></i>
                    @else
                    <i class="fa-solid fa-circle py-2 px-3 mx-3 fs-6 draggable-handle"></i>
                    @endif
                </div>
            </div>
        </div>
    <!-- END:TASK -->
    <!-- BEGIN:SUB-TASK -->
    @foreach ($content->subtasks as $subtask)
    <div class="mb-2 ms-12">
        <div class="bg-white rounded p-0 d-flex align-items-center justify-content-between mb-2 shadow-list dmk-tasks h-35px task-list task-on-subtask z-index-1">
        <div class="d-flex align-items-center justify-content-between w-100 h-100">
            <div class="d-flex align-items-center h-100 w-100 task-left-side">
                <div style="background: {{ $subtask->project->color }}" class="rounded-start h-100 w-40px d-flex align-items-center justify-content-center color-task">
                    <div class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input w-15px h-15px cursor-pointer check-task" disabled type="checkbox" value="1" style="border-radius: 3px" @if($subtask->checked == true) checked @endif/>
                    </div>
                </div>
                <div class="d-flex align-items-center h-100 w-100 div-name-task z-index-9">
                <div class="w-20px"></div>
                <div class="d-block min-w-300px w-100">
                        <p class="text-gray-600 text-hover-primary fs-6 lh-1 fw-normal p-0 mb-0 cursor-pointer border-0 w-100 task-name show-task" data-task="{{ $subtask->task_id }}" style="margin-top: 1px;">{{ $subtask->name }}</p>
                    </div>
                </div>
            </div>
            <span>
            <i class="fa-solid fa-font-awesome p-2 
                @if ($subtask->priority == 0)
                text-gray-300
                @elseif($subtask->priority == 1)
                text-warning
                @elseif($subtask->priority == 2)
                text-info
                @elseif($subtask->priority == 3)
                text-danger
                @endif
                me-5"></i>
            </span>
        </div>
        <div class="d-flex align-items-center h-100">
            <!-- SEPARATOR -->
            <div class="separator-vertical h-100"></div>
            <!-- SEPARATOR -->
            <div class="w-125px text-center designated-div">
                <div class="symbol symbol-30px symbol-circle" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-start">
                    <div class="symbol symbol-25px symbol-circle me-2">
                        <img alt="Pic" src="{{ findImage('users/' . $subtask->designated_id . '/' . 'perfil-35px.jpg') }}" class="designated">
                    </div>
                </div>
            </div>
            <div class="d-flex p-0 align-items-center justify-content-center cursor-pointer h-100 w-150px rounded-0 actual-status" style="background: {{ $subtask->statusInfo->color }}">
                <div class="w-100 h-100 d-flex align-items-center justify-content-center" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-start">
                    <p class="text-white fw-bold m-0 status-name">{{ $subtask->statusInfo->name }}</p>
                </div>
            </div>
            <input type="text" class="form-control border-0 form-control-sm flatpickr w-auto text-center w-200px task-date" disabled data-task="{{ $subtask->id }}" placeholder="Prazo da tarefa" value="@if($subtask->date) {{ date('Y-m-d H:i:s', strtotime($subtask->date)) }} @endif"/>
        </div>
            <!-- SEPARATOR -->
            <div class="separator-vertical h-100"></div>
            <!-- SEPARATOR -->
            <div>
            <i class="fa-solid fa-bars-staggered text-gray-300 py-2 px-3 mx-3 fs-6"></i>
            </div>
        </div>
    </div>
    @endforeach
    <!-- END:SUB-TASK -->
    </div>
@endforeach