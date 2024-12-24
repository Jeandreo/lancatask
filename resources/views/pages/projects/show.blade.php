@extends('layouts.app')

@section('Page Title', $project->name)

@section('custom-head')
<script src="{{ asset('assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
@endsection

@section('content')
@if ($project->modules->count())
    @foreach ($project->modules as $module)
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
                @if ($project->tasks()->whereNull('task_id')->count())
                    @foreach ($project->tasks()->where('status', 1)->whereNull('task_id')->where('checked', false)->orderBy('order', 'ASC')->orderBy('updated_at', 'DESC')->get() as $task)
                        @include('pages.tasks._tasks')
                    @endforeach
                @endif
                <div class="no-tasks" @if ($project->tasks()->where('status', 1)->whereNull('task_id')->where('checked', false)->count()) style="display: none;" @endif>
                    <div class="rounded bg-light d-flex align-items-center justify-content-center h-50px">
                        <div class="text-center">
                            <p class="m-0 text-gray-600 fw-bold text-uppercase">Sem tarefas ainda nesse projeto</p>
                        </div>
                    </div>
                </div>
            </div>
            <form action="#" method="POST" class="send-tasks">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="module_id" value="{{ $module->id }}">
                <input type="text" name="name" class="form-control form-control-solid w-100 rounded mt-5" placeholder="Inserir nova tarefa" required>
            </form>
        </div>
    </div>
    @endforeach
@else
<div class="card mb-6">
    <div class="card-body p-5">
        <h2 class="text-center fs-2x fw-bolder text-gray-700 my-6">Sem módulos</h2>
    </div>
</div>
@endif
<div class="modal fade" data-bs-focus="false" id="modal_task">
    <div class="modal-dialog modal-dialog-centered rounded">
        <div class="modal-content rounded bg-transparent" id="load-task">
            {{-- LOAP TASK HERE --}}
            {{-- LOAP TASK HERE --}}
            {{-- LOAP TASK HERE --}}
        </div>
    </div>
</div>
@endsection

@section('custom-footer')
<script>
	// PROJECT ID
	var projectId = {{ $contents->id ?? 0 }};

	// DRAGGABLE
	function draggable(){
		var containers = document.querySelectorAll(".draggable-zone");
		if (containers.length === 0) return false;
		var swappable = new Sortable.default(containers, {
			draggable: ".draggable",
			handle: ".draggable .draggable-handle",
			mirror: {
				constrainDimensions: true,
			},
		});

		// ON STOP DRAG
		swappable.on('drag:stopped', function(event) {

			// GET DIV OF ELEMENT
			var movedDiv = event.originalSource;

			// GET ID OF TASK
			var taskId = $(movedDiv).data('task');

			// GET PROJECT
			var draggableDropped = $(movedDiv).closest('.draggable-zone');

			// TYPE DRAGGABLE
			var draggableType = draggableDropped.data('type');

			// GET PROJECT
			var projectId = draggableDropped.data('project');

			// START
			var tasksOrderIds = [];

			// GET IDS OF TASKS ONLY DRAGGABLE-ZONE
			draggableDropped.find('.task-list').each(function() {
				// OBTEM ITEM
				var item = $(this).data('task');
				tasksOrderIds.push(item);
			});

			// HIDE NO TASKS IN ZONE DROPPED
			draggableDropped.find('.no-tasks').fadeOut();

			// AJAX
			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				type:'PUT',
				url: "{{ route('tasks.order') }}",
				data: {
					_token: @json(csrf_token()),
					project_id: projectId,
					task_id: taskId,
					tasksOrderIds: tasksOrderIds
				},
				success: function(response){

					// CHANGE COLOR PROJECT ON TASK
					$(movedDiv).find('.color-task').css('background', response['color']);

					// GET ZONE INITIAL
					var startZone = $('#project-tasks-' + response['startProject']);

					// COUNT TASKS IN ZONE
					var tasksCount = startZone.find('.task-list').length;

					// IF NO TASKS IN ZONE
					if (tasksCount == 0) startZone.find('.no-tasks').fadeIn();

				}
			});

		});

	}

	draggable();

</script>
@include('pages.tasks._javascript')
@endsection
