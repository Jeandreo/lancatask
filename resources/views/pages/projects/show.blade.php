@extends('layouts.app')

@section('Page Title', $project->name)

@section('custom-head')
<script src="{{ asset('assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
@endsection

@section('content')
<div class="card mb-4" id="section-filters">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <input class="form-control form-control-solid" placeholder="Nome da tarefa" id="name"/>
            </div>
            <div class="col">
                <select class="form-select form-select-solid cursor-pointer" data-control="select2" data-placeholder="Status" name="status[]" multiple id="status">
                    <option value=""></option>
                    @foreach ($project->statuses as $statu)
                        <option value="{{ $statu->id }}">{{ $statu->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-1">
                <input type="button" class="btn btn-primary btn-active-danger me-4 w-100" value="Filtrar" id="filtrar"></input>
            </div>
        </div>
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

    // SHOW TASK
    $(document).on('click', '#filtrar', function(e){
        filterModules();
    });


    // Filtrar dados
    $('#section-filters input, #section-filters select').on('change', function() {
        filterModules();
    });

    // Obtém tarefas dos módulos
    filterModules();

    // Filtra módulos
    function filterModules(){

        // Projeto
        var projectId = "{{ $project->id }}";

        // AJAX
        $.ajax({
            type:'GET',
            url: "{{ route('modules.filter', '') }}/" + projectId,
            data: {
                name: $('#name').val(),
                status: $('#status').val(),
            },
            success:function(data) {
                data.forEach(element => {
                    $('#project-tasks-' + element.id).html(element.html);
                    generateFlatpickr();
                    KTMenu.createInstances();
                });
            }
        });

    }


</script>
@include('pages.tasks._javascript')
@endsection
