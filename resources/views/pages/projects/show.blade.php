@extends('layouts.app')

@section('Page Title', $project->name)

@section('custom-head')
<script src="{{ asset('assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h2>Lançamento 01/02/2024</h2>
        <span>Frase</span>
        <span>Emoji</span>
    </div>
</div>
<div class="modules">
    @if ($project->modules()->where('status', true)->count())
        @foreach ($project->modules()->where('status', true)->get() as $module)
            @include('pages.projects._module')
        @endforeach
    @endif
</div>
<div class="d-flex">
    <div class="card mb-6 text-hover-primary text-gray-700 cursor-pointer" id="add-module" data-project="{{ $project->id }}">
        <div class="card-body p-5 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center">
                <i class="fa-solid fa-circle-plus fs-2x me-4 text-gray-600"></i>
                <div class="d-block">
                    <p class="fs-3 fw-bolder text-uppercase mb-0 lh-1">Adicionar Módulo</p>
                    <p class="m-0 lh-1">Adicione uma nova seção a esse projeto</p>
                </div>
            </div>
        </div>
    </div>
</div>
@include('pages.tasks._modals')
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
			var moduleId = draggableDropped.data('module');

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
					module_id: moduleId,
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
