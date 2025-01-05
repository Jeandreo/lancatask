@extends('layouts.app')

@section('Page Title', 'Agenda')

@section('content')
    <div class="d-flex justify-content-between mb-6">
        <a href="{{ route('dashboard.index') }}" class="btn btn-sm fw-bold btn-secondary">Voltar</a>
        <button class="btn btn-sm fw-bold btn-primary btn-active-danger" data-bs-toggle="modal" data-bs-target="#modal_meeting">Cadastrar na agenda</button>
    </div>
    <div class="card">
        <div class="card-body">
            <div id="full-calendar">
                {{-- CARREGA CALENDÁRIO --}}
                {{-- CARREGA CALENDÁRIO --}}
                {{-- CARREGA CALENDÁRIO --}}
            </div>
        </div>
    </div>
	@include('pages.agenda._modals')
@endsection

@section('custom-footer')
@parent
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/rrule@2.6.4/dist/es5/rrule.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@6.1.15/index.global.min.js'></script>
    <script>

        // Declara variável global
        var calendar;

        // Declara função global
        function loadFullCalendar(){

            // Função para formatar a data no formato Y-m-d
            function formatDate(date) {
                let year = date.getFullYear();
                let month = String(date.getMonth() + 1).padStart(2, '0');
                let day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Função para formatar o horário no formato H:i:s
            function formatTime(date) {
                let hours = String(date.getHours()).padStart(2, '0');
                let minutes = String(date.getMinutes()).padStart(2, '0');
                let seconds = String(date.getSeconds()).padStart(2, '0');
                return `${hours}:${minutes}:${seconds}`;
            }

            // Obtém eventos
            var events = @json($agenda);

            // initialize the calendar
            var calendarEl = document.getElementById('full-calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                height: 900,
                locale: 'pt-br',
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay"
                },
                initialView: 'dayGridMonth',
                navLinks: false,
                selectable: false,
                selectMirror: false,
                editable: false,
                dayMaxEvents: false,
                nowIndicator: false,
                events: events,
                businessHours: {
                    daysOfWeek: [0, 1, 2, 3, 4, 5, 6],
                    startTime: '00:00',
                    endTime: '24:00',
                },
                slotDuration: '00:30:00',
                slotLabelInterval: '01:00:00',
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    omitZeroMinute: false,
                    meridiem: false
                },
                slotLaneDidMount: function(slotLane) {
                    // Destaca hora do almoço
                    var hour = slotLane.date.getHours();
                    if (hour >= 12 && hour < 13) {
                        slotLane.el.classList.add('lunch-time');
                    }
                },
                eventDrop: function(info) {


                    // Atualiza a data no calendário ao arrastar tarefa
                    var start   = info.event.start;
                    var end     = info.event.end
                    var type    = info.event.extendedProps.type;
                    var id      = info.event.extendedProps.id;
                    var allDay  = info.event.allDay;

                    updateCalendar(id, type, start, end, allDay);

                },
                eventResize: function(info) {

                    // Obtém data inicial e final após o redimensionamento
                    var start = info.event.start;
                    var end = info.event.end;

                    // Obtém dados para atualização
                    var type   = info.event.extendedProps.type;
                    var id     = info.event.extendedProps.id;

                    console.log(id, type, start, end);

                    // Realiza a requisição AJAX
                    updateCalendar(id, type, start, end);

                },
                eventClick: function(info) {

                    // Obtém dados
                    var infoProps = info.event.extendedProps;
                    var type = infoProps.type;
                    var id   = infoProps.id;

                    // Abre a tarefa
                    if(type == 'tasks'){
                        openTask(id);
                    } else if(type == 'meetings') {
                        openMetting(id);
                    }

                },
                eventDidMount: function(info) {

                    // Obtém dados
                    var infoProps = info.event.extendedProps;
                    var type = infoProps.type;

                    if(type == 'meetings'){
                        var eventEnd = info.event.end ? info.event.end : info.event.start;
                        var now = new Date();

                        if (eventEnd < now) {
                            // Evento já passou, altera a cor
                            info.el.style.backgroundColor = '#d7d7d7';
                            info.el.style.borderColor = '#d7d7d7';
                        }
                    }

                },
                // Evento para capturar a mudança de visualização
                datesSet: function(info) {
                    localStorage.setItem('selectedView', info.view.type);
                },
                eventContent: function(arg) {
                    // Cria uma DIV para renderizar o título
                    let div = document.createElement('div');

                    // Obtém título HTML e insere na DIV
                    div.className = 'p-1';

                    // Cria badge
                    var color = arg.event.backgroundColor;
                    var badge = '<div class="fc-daygrid-event-dot ms-0 me-2" style="border-color: ' + color + ';"></div>';

                    // Tipo
                    var type = arg.event.extendedProps.type;

                    // Formata as horas de início e fim
                    var start = arg.event.start;
                    var end = arg.event.end;
                    var startTime = start ? start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                    var endTime = end ? end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';

                    // Adiciona as horas ao título
                    if (startTime && endTime) {
                        time = '<span class="event-time fw-bold">' + startTime + ' - ' + endTime + '</span>';
                    } else if (startTime) {
                        time = '<span class="event-time fw-bold">' + startTime + '</span>';
                    }

                    // Monta a string com o título e horários
                    var eventTitle = '';
                    if (type === 'meetings') {
                        eventTitle = time + '<div class="d-flex align-items-center overflow-hidden mt-n1">' + badge + arg.event.extendedProps.htmlTitle + '</div>';
                    } else {
                        eventTitle = time + '<div class="d-flex align-items-center overflow-hidden mt-n1">' + badge + arg.event.title + '</div>';
                    }

                    // Define o conteúdo da DIV
                    div.innerHTML = eventTitle;

                    // Retorna formatado
                    return { domNodes: [div] };
                }

            });

            calendar.render();

            var preFilter = "{{ $filterFor }}";

            // Função para filtrar eventos
            function filterEvents(filterFor) {
                var filteredEvents;
                if (filterFor === "clean" || filterFor == '') {
                    filteredEvents = events; // Todos os eventos
                } else {
                    filteredEvents = events.filter(function(event) {
                        return event.for == filterFor;
                    });
                }
                calendar.removeAllEvents();
                calendar.addEventSource(filteredEvents);
            }

            filterEvents(preFilter);

            // Clique no botão de filtro
            $('.filter-user').on('click', function() {
                var user = $(this).data('userid');
                filterEvents(user);
            });

            // Atualiza as datas do calendário
            function updateCalendar(id, type, start, end = null, isAllDay = false){

                // Separa a data inicial e a hora inicial
                var dateStart = formatDate(start);
                var hourStart = formatTime(start);

                if(end){
                    // Separa a data final e a hora final
                    var dateEnd = formatDate(end);
                    var hourEnd = formatTime(end);
                }

                $.ajax({
                    type: 'PUT',
                    url: "{{ route('agenda.calendar.update', '') }}/" + id,
                    data: {
                        _token: @json(csrf_token()),
                        type: type,
                        date_start: dateStart,
                        date_end: dateEnd ?? null,
                        hour_start: hourStart,
                        hour_end: hourEnd ?? null,
                        all_day: isAllDay,
                    },
                });

            }

        }

        // Recarrega os eventos
        function reloadEvents(eventsType, find, user = null) {
            $.ajax({
                type: 'POST',
                url: "#",
                data: {eventsType: eventsType, find: find, userId: user},
                success: function(events) {
                    if (calendar) {
                        calendar.removeAllEvents();
                        calendar.addEventSource(events);
                    }
                },
            });
        }

        // Chama calendário
        loadFullCalendar();

    </script>
@endsection
