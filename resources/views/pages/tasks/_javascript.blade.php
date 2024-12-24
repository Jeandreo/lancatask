<script>

	function callFunctions(){
		generateFlatpickr();
		KTMenu.createInstances();
	}

    // DRAGGABLE
	function draggableSubTasks(zoneId){
		var containers = document.querySelectorAll(zoneId);
		if (containers.length === 0) return false;
		var swappableSub = new Sortable.default(containers, {
			draggable: ".draggable-sub",
			handle: ".draggable-sub .draggable-sub-handle",
			mirror: {
				constrainDimensions: true,
			},
		});

		// ON STOP DRAG
		swappableSub.on('drag:stopped', function(event) {


            // GET DIV OF ELEMENT
			var movedDiv = event.originalSource;

			// GET PROJECT
			var draggableDropped = $(movedDiv).closest('.subtasks-zone');

			// START
			var tasksOrderIds = [];

			// GET IDS OF TASKS ONLY DRAGGABLE-ZONE
			draggableDropped.find('.task-list').each(function() {
				var item = $(this).data('task');
				tasksOrderIds.push(item);
			});

			// AJAX
			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				type:'PUT',
				url: "{{ route('tasks.order') }}",
				data: {
					_token: @json(csrf_token()),
					tasksOrderIds: tasksOrderIds
				},
				success: function(response){
				}
			});


        });

	}

    $('.subtasks-zone').each(function() {
        var zoneId = $(this).attr('id');
        draggableSubTasks('#' + zoneId);
    })

    // Sistema que transforma subtarefa em separador
    $(document).on('click', '.transform-in-separator', function(){
        // Encontra subtarefa
        var subtask = $(this).closest('.dmk-div-task');
        var subtaskId = subtask.find('.task-on-subtask').data('task');
        var isSeparator = subtask.data('separator');

        if (isSeparator == 1) {
            subtask.find('.sub-task-icons').toggleClass('rounded rounded-start');
            subtask.find('.separator-name').fadeOut('slow', function(){
                subtask.find('.separator-name').css('width', '0');
                subtask.find('.not-separator-after').fadeIn('slow', function(){
                    subtask.find('.sub-task-icons').css('width', '');
                    subtask.find('.not-separator').css('width', '');
                    subtask.find('.not-separator').fadeIn('slow');
                });
            });
            subtask.data('separator', 0);
        } else {
            // Transformar em separador
            subtask.find('.sub-task-icons').toggleClass('rounded-start rounded');

            subtask.find('.not-separator').fadeOut('slow', function(){
                subtask.find('.sub-task-icons').css('width', '100%');
                subtask.find('.not-separator-after').fadeOut('slow', function(){
                    subtask.find('.separator-name').css('width', '100%');
                    subtask.find('.separator-name').fadeIn('slow');
                });
            });
            subtask.data('separator', 1);
        }

        // AJAX
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'PUT',
            url: "{!! route('tasks.separator', '') !!}/" + subtaskId,
        });
    });

    // UPDATE STATUS
    $(document).on('click', '.send-tasks-projects', function(e){
        // GET DATA
        var taskId = $(this).data('task');
        var projectId = $(this).data('project');
        var projectName = $(this).text();
        var projectColor = $(this).data('color');

        // GET ACTUAL PROJECT CONTAINER
        var projectContainer = $(this).closest('.send-tasks');

        // Altera nome e cor
        projectContainer.find('[name="project_id"]').val(projectId);
        projectContainer.find('.project-name').text(projectName);
        projectContainer.find('.background-project').css('background', projectColor);

    });


    // SEND NEW TASK
    $(document).on('submit', '.send-tasks', function(e){

        // STOP EVENT
        e.preventDefault();

        // GET TITLE OF TASK
        var name        = $(this).find('[name="name"]').val();
        var projectId   = $(this).find('[name="project_id"]').val();
        var moduleId    = $(this).find('[name="module_id"]').val();
        var date        = $(this).find('[name="date"]').val();

        // CLEAN INPUT
        $(this).find('[name="name"]').val('');

        // FIND WHERE INSERT
        var divNoTask = $(this).closest('.card-body').find('.no-tasks');

        // AJAX
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            url: "{{ route('tasks.store') }}",
            data: {project_id: projectId, module_id: moduleId, date: date, name: name},
            success: function(data){

                // Esconde div sem tarefas
                $('.no-tasks').hide();

                // AJAX
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'GET',
                    url: "{{ route('tasks.show.one', '') }}/" + data['id'],
                    success: function(taskDiv){
                        divNoTask.before(taskDiv);
                        callFunctions();
                    }
                });

            }
        });

    });

    // LOAD SOUND
    var check = new Audio('{{ asset("assets/media/sounds/task-checked.mp3") }}');
    var stand = new Audio('{{ asset("assets/media/sounds/task-stand.mp3") }}');
    var remove = new Audio('{{ asset("assets/media/sounds/task-remove.mp3") }}');
    var open = new Audio('{{ asset("assets/media/sounds/task-open.mp3") }}');

    // SAVE STATUS CHECKED
    $(document).on('click', '.check-task', function(){

        // GET TASK
        var taskId = $(this).data('task');
        var isMain = $(this).hasClass('task-main');
        var subtask = $(this).closest('.task-left-side').find('.input-name');
        var checked = $(this).is(':checked');

        // IF TASK MAIN
        if(isMain){
            // SELECT DIV OF TASK
            var taskDiv = $(this).closest('.dmk-div-task');

            // ADD ANIMATION AND REMOVE TASK
            taskDiv.addClass('slide-up');
            setTimeout(function() {
                taskDiv.remove();
            }, 500);

        } else {
            subtask.toggleClass('text-decoration-line-through ');
        }

        // IF CHECKED
        if(checked){
            // PLAY SOUND
            check.play();
        }

        // AJAX
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            url: "{{ route('tasks.check') }}",
            data: {task_id: taskId},
        });

    });

    // SAVE STATUS CHECKED
    $(document).on('click', '.add-subtasks', function(){

        // GET TASK
        var taskId = $(this).data('task');
        var projectId = $(this).data('project');

        // DIV
        var divSubtasks = $(this).closest('.draggable');

        // AJAX
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            url: "{{ route('tasks.subtask') }}",
            data: {task_id: taskId, project_id: projectId},
            success: function(data){
                divSubtasks.append(data);
                generateFlatpickr();
            }
        });

    });

    // SHOW INPUT PHRASE
    $(document).on('focus', '.input-name', function(){
        $(this).next('.input-phrase').fadeIn();
    });

    // HIDE INPUT PHRASE
    $(document).on('blur', '.input-phrase input', function(){

        var text = $(this).val().trim();
        if(text == ''){
            $(this).closest('.input-phrase').fadeOut().css('border-bottom', 'dashed 1px #bbbdcb63 !important');
        } else {
            $(this).css('border-bottom', '');
        };
    });

    // UPDATE TITLE AND PHRASE
    $(document).on('change', '.input-name, .task-phrase, .task-description', function(){

        // GET DATA
        var input = $(this).attr('name');
        var value = $(this).val();
        var taskId = $(this).data('task');

        // IF RENAME TASK
        if(input == 'name'){
            $(this).closest('.div-name-task').find('.task-name').text(value);
        }

        // AJAX
        $.ajax({
            type: 'PUT',
            url: "{!! route('tasks.update.ajax', '') !!}/" + taskId,
            data: {_token: @json(csrf_token()), input: input, value: value},
        });

    });

    // UPDATE TITLE AND PHRASE
    $(document).on('click', '.task-priority', function(){

        // GET TEXT
        var taskId = $(this).data('task');

        // SAVE FLAG
        var flagHtml = $(this).find('i');

        // AJAX
        $.ajax({
            type: 'PUT',
            url: "{{ route('tasks.priority') }}",
            data: {_token: @json(csrf_token()), task_id: taskId},
            success: function(data){

                // ALTERA PRIORIDADE
                if (data == 1){
                    flagHtml.removeClass('text-gray-300').addClass('text-warning');
                } else if (data == 2){
                    flagHtml.removeClass('text-warning ').addClass('text-info');
                } else if (data == 3){
                    flagHtml.removeClass('text-info').addClass('text-danger');
                } else {
                    flagHtml.removeClass('text-danger').addClass('text-gray-300');
                }

            }
        });

    });

    // UPDATE DESIGNATED TASK
    $(document).on('click', '.task-designated', function(){

        // GET TEXT
        var taskId = $(this).data('task');
        var designated = $(this).data('designated');

        // SAVE FLAG
        var img = $(this).closest('.designated-div').find('.designated');

        // AJAX
        $.ajax({
            type: 'PUT',
            url: "{{ route('tasks.designated') }}",
            data: {_token: @json(csrf_token()), task_id: taskId, designated_id: designated},
            success: function(data){
                img.attr('src', data);
            }
        });

    });

    // UPDATE STATUS
    $(document).on('click', '.tasks-status', function(e){

        // GET DATA
        var taskId = $(this).data('task');
        var statusId = $(this).data('status');

        // GET ACTUAL STATUS
        var status = $(this).closest('.actual-status');

        // AJAX
        $.ajax({
            type:'PUT',
            url: "{{ route('tasks.status') }}",
            data: {_token: @json(csrf_token()), task_id: taskId, status_id: statusId},
            success:function(data) {
                // CHANGE TO NEW COLOR AND NAME STATUS
                status.css('background', data['color']);
                status.find('.status-name').text(data['name']);
            }
        });

    });

    // UPDATE DATE
    $(document).on('change', '.task-date', function(){

        // GET NEW DATE
        var date = $(this).val();
        var taskId = $(this).data('task');

        // GET ACTUAL DATE
        var currentDate = new Date();

        // FORMAT DATE
        var taskDate = new Date(date);

        // Obtenha as datas sem as horas, minutos e segundos no horário UTC
        var taskDateWithoutTime = new Date(taskDate);
        taskDateWithoutTime.setHours(0, 0, 0, 0);

        var currentDateWithoutTime = new Date(currentDate);
        currentDateWithoutTime.setHours(0, 0, 0, 0);

        // GET DIFFERENCE
        var difference = Math.floor((taskDateWithoutTime - currentDateWithoutTime) / (1000 * 60 * 60 * 24)) + 1;


        // REMOVE PREVIOUS CLASS
        $('.task-date-' + taskId).removeClass('text-danger text-primary text-success text-info text-gray-700');

        // VERIIFY DIFERENCE
        if (difference < 0) {
            $('.task-date-' + taskId).addClass('text-danger');
        } else if (difference == 0) {
            $('.task-date-' + taskId).addClass('text-success');
        } else if (difference <= 2) {
            $('.task-date-' + taskId).addClass('text-primary');
        } else {
            $('.task-date-' + taskId).addClass('text-gray-700');
        }

        // AJAX
        $.ajax({
            type:'PUT',
            url: "{{ route('tasks.date') }}",
            data: {_token: @json(csrf_token()), task_id: taskId, date: date},
        });

    });

    function showTask(id){

        // AJAX
        $.ajax({
            type:'POST',
            url: "{{ route('tasks.show') }}",
            data: {_token: @json(csrf_token()), task_id: id},
            success:function(data) {

                //  REPLACE CONTENT
                $('#load-task').html(data);

                // CHANGE TO NEW COLOR AND NAME STATUS
                $('#modal_task').modal('show');

                // LOAD COMMENTS
                loadComments(id);

                // LOAD EDITOR
                loadEditorText();

            }
        });

    }

    function loadComments(id){

        // AJAX
        $.ajax({
            type:'POST',
            url: "{{ route('comments.show') }}",
            data: {_token: @json(csrf_token()), task_id: id},
            success:function(data) {

                //  REPLACE CONTENT
                $('#results-comments').html(data);

            }
        });

    }

    // SHOW TASK
    $(document).on('submit', '#send-comment', function(e){

        // PARA EVENTO
        e.preventDefault();

        // GET DATA
        var taskId = $(this).data('task');
        var text = $(this).find('[name="text"]').val();

        // AJAX
        $.ajax({
            type:'POST',
            url: "{{ route('comments.store') }}",
            data: {_token: @json(csrf_token()), task_id: taskId, text: text},
            success:function(data) {
                loadComments(taskId);
                textarea.setData('');
                $('#results-comments').scrollTop(0);
            }
        });

    });

    // SHOW TASK
    $(document).on('click', '.destroy-comment', function(e){

        // PARA EVENTO
        e.preventDefault();

        // GET DATA
        var url = $(this).attr('href');
        var taskId = $(this).data('task');

        // AJAX
        $.ajax({
            type:'PUT',
            url: url,
            data: {_token: @json(csrf_token())},
            success:function(data) {
                loadComments(taskId);
            }
        });

    });

    // SHOW TASK
    $(document).on('click', '.show-task', function(){

        // GET DATA
        var taskId = $(this).data('task');

        // open.play();

        // EXIBE TASK
        showTask(taskId);

    });

    // SHOW SUBTASKS
    $(document).on('click', '.show-subtasks', function(){

        // GET TASK
        var task = $(this).data('task');

        // SHOW ZONE
        $('.subtasks-zone-' + task).toggle();

        // VERIFY IF HAS "rotate-90"
        var hasClass = $(this).hasClass('rotate-90');

        // ADD OR REMOVE CLASS
        if(hasClass){
            $(this).removeClass('rotate-90');
        } else {
            $(this).addClass('rotate-90');
        }

        // AJAX
        $.ajax({
            type:'POST',
            url: "{{ route('tasks.show.subtasks') }}",
            data: {_token: @json(csrf_token()), task_id: task},
            success:function(data) {
                console.log(data);
            }
        });

    });

    // SHOW SUBTASKS
    $(document).on('click', '.remove-date', function(){

        // GET TASK
        var task = $(this).data('task');

        // GET TASK
        $(this).closest('.position-relative').find('.task-date').val('');

        // AJAX
        $.ajax({
            type:'POST',
            url: "{{ route('tasks.time') }}",
            data: {_token: @json(csrf_token()), task_id: task},
            success:function(data) {
                console.log('Data removida');
            }
        });

    });

    // SHOW SUBTASKS
    $(document).on('click', '.tasks-destroy', function(){

        // GET TASK
        var task = $(this).data('task');

        // GET DIV TASK
        var taskDiv = $(this).closest('.dmk-div-task');

        // PLAY SOUND
        remove.play();

        // REMOVE
        taskDiv.remove();

        // AJAX
        $.ajax({
            type:'POST',
            url: "{{ route('tasks.destroy') }}",
            data: {_token: @json(csrf_token()), task_id: task},
            success:function(data) {

            }
        });

    });

    // SHOW SUBTASKS
    $(document).on('click', '.stand-by', function(){

        // GET TASK
        var task = $(this).data('task');

        // GET DIV TASK
        var taskDiv = $(this).closest('.dmk-div-task');

        // PLAY SOUND
        stand.play();

        // REMOVE
        taskDiv.remove();

        // AJAX
        $.ajax({
            type:'POST',
            url: "{{ route('tasks.stand.by') }}",
            data: {_token: @json(csrf_token()), task_id: task},
            success:function(data) {
            }
        });

    });

</script>
