<div class="row py-2">
    <div class="col-2">
        <label class="form-label fs-6 fw-bold text-gray-900 mb-3 required">Título/Cor:</label>
    </div>
    <div class="col-10">
        <div class="row">
            <div class="col-9">
                <input type="text" class="form-control form-control-solid" placeholder="Nome do evento" name="name" value="{{ $content->name ?? old('name') }}" maxlength="255" required>
            </div>
            <div class="col-3">
                <div class="d-flex p-0 align-items-center justify-content-center cursor-pointer h-100 rounded actual-color" style="background: {{ $content->color ?? '#007BFF' }};">
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-start">
                        <p class="text-white fw-bold m-0 text-center name-color">
                            @if (isset($content->color))
                                @switch($content->color)
                                    @case('#007BFF')
                                        SDR
                                    @break
                                    @case('#28A745')
                                        Closer
                                    @break
                                    @case('#FFC107')
                                        Assessoria
                                    @break
                                    @case('#DC3545')
                                        Briefing
                                    @break
                                    @case('#6F42C1')
                                        Planejamento
                                    @break
                                    @case('#FD7E14')
                                        Weekly
                                    @break
                                    @case('#E83E8C')
                                        All Hands
                                    @break
                                @endswitch
                            @else
                                SDR
                            @endif
                        </p>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-250px py-4" data-kt-menu="true">
                            @foreach ([
                                'SDR'          => "#007BFF", // Azul
                                'Closer'       => "#28A745", // Verde
                                'Assessoria'   => "#FFC107", // Amarelo
                                'Briefing'     => "#DC3545", // Vermelho
                                'Planejamento' => "#6F42C1", // Roxo
                                'Weekly'       => "#FD7E14", // Laranja
                                'All Hands'    => "#E83E8C", // Rosa
                            ] as $name => $color)
                                <div class="menu-item px-3 mb-2">
                                    <span data-color="{{ $color }}" class="menu-link px-3 d-block text-center agenda-color" style="background: {{ $color }}; color: white">
                                        {{ $name }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <input type="hidden" name="color" value="{{ $content->color ?? '#007BFF' }}" required>
            </div>
        </div>
    </div>
</div>
<div class="row py-2">
    <div class="col-2">
        <label class="form-label fs-6 fw-bold text-gray-900 mb-3">Descrição:</label>
    </div>
    <div class="col-10">
        <textarea class="form-control form-control-solid" name="description" cols="30" rows="3" placeholder="Sobre oque será o evento?">@if(isset($content)){{ $content->description }}@endif</textarea>
    </div>
</div>
<div class="row py-2 div-pontual">
    <div class="col-2">
        <label class="form-label fs-6 fw-bold text-gray-900 required">Início/Fim: </label>
    </div>
    <div class="col-6">
        <input class="form-control form-control-solid flatpickr-date cursor-pointer text-center" placeholder="00/00/0000" type="text" name="date" value="@if(isset($content)){{ date('d/m/Y', strtotime($content->date_start)) }}@endif" required>
    </div>
    <div class="col-2">
        <input class="form-control form-control-solid flatpickr-time-custom cursor-pointer text-center" placeholder="00:00" type="text" name="hour_start" value="@if(isset($content)){{ "$content->hour_start" }}@endif" required>
    </div>
    <div class="col-2">
        <input class="form-control form-control-solid flatpickr-time-custom cursor-pointer text-center" placeholder="00:00" type="text" name="hour_end" value="@if(isset($content)){{ "$content->hour_end" }}@endif" required>
    </div>
</div>

@if (!isset($content) || $content->id_google)
    <div class="row py-2">
        <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
            <label class="form-check-label form-label fs-6 fw-bold text-gray-900 mb-0 me-6" for="googleCalendar">
                @if (!isset($content))
                Incluir no Google Calendar
                @else
                Editar no Google Calendar
                @endif
            </label>
            @if (!isset($content))
            <input class="form-check-input cursor-pointer" type="checkbox" value="1" checked id="googleCalendar" name="send_google" @if(isset($content) && $content->id_google) checked @endif/>
            @endif
        </div>
    </div>
    <div class="row py-2 select-members">
        <div class="col-2">
            <label class="form-label fs-6 fw-bold text-gray-900 mb-3 required">Membros do time:</label>
        </div>
        <div class="col-10">
            <select class="form-select form-select-solid" name="users[]" multiple data-control="select2" data-placeholder="Selecione" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @if(isset($content) && $content->members()->where('type', 'user')->get()->contains('member_id', $user->id)) selected @endif>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row py-2 select-clients">
        <div class="col-2">
            <label class="form-label fs-6 fw-bold text-gray-900 mb-3">Clientes:</label>
        </div>
        <div class="col-10">
            <select class="form-select form-select-solid" name="clients[]" multiple data-control="select2" data-placeholder="Selecione">
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" @if(isset($content) && $content->members()->where('type', 'client')->get()->contains('member_id', $client->id)) selected @endif>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row py-2 select-clients">
        <div class="col-2">
            <label class="form-label fs-6 fw-bold text-gray-900 mb-3">Emails:</label>
        </div>
        <div class="col-10">
            <input class="form-control form-control-solid tagify" name="emails_additional" value="{{ isset($content) ? $content->extra_emails : '' }}"/>
        </div>
    </div>
@endif

@section('custom-footer')
@parent
<script>

var emailsTag = document.querySelector(".tagify");
new Tagify(emailsTag);

$(document).on('click', '.agenda-color', function(){
    var color = $(this).data('color');
    var name = $(this).text();
    $('.actual-color').css('background', color);
    $('[name="color"]').val(color);
    $('.name-color').text(name);
});

function toggleParticipantsRequirement(checked) {
    // mostra / esconde
    $('.select-members, .select-clients')[ checked ? 'show' : 'hide' ]();
    // adiciona ou remove o required nos <select>
    $('.select-members select').prop('required', checked);
  }

    $(document).on('change', '[name="send_google"]', function(){
        if($(this).is(':checked')){
            $('.select-members').show();
            $('.select-clients').show();
        } else {
            $('.select-members').hide();
            $('.select-clients').hide();
        }
    });

    // FORMAT OPTIONS
    var optionFormat = function(item) {
        if ( !item.id ) {
            return item.text;
        }

        var span = document.createElement('span');
        var imgUrl = item.element.getAttribute('data-kt-select2-user');
        var template = '';

        template += '<img src="' + imgUrl + '" class="rounded-circle h-20px me-2" alt="image"/>';
        template += item.text;

        span.innerHTML = template;

        return $(span);
    }

    $(document).on('change', '#googleCalendar', function() {
        toggleParticipantsRequirement( $(this).is(':checked') );
    });

    $(document).ready(function(){
        toggleParticipantsRequirement( $('#googleCalendar').is(':checked') );
        // SELECT 2
        $('.select-paticipants').select2({
            templateSelection: optionFormat,
            templateResult: optionFormat
        });
    })

    // HIDDEN COLUMNS
    $(document).on('change', '[name="general"]', function(){

        // GET GENERAL
        var general = $(this).val();

        // IF DATE END
        if(general == 0){
            $('.select-div select').prop('required', true);
            $('.select-div').show();
        } else {
            $('.select-div select').prop('required', false);
            $('.select-div').hide()
        }

    });

    $(document).on('change', '[name="recurrent"]', function(){

        // GET GENERAL
        var recurrent = $(this).val();

        // IF DATE END
        if(recurrent == '0'){
            $('.div-recurrent').hide();
            $('.div-pontual').show();
        } else {
            $('.div-recurrent').show()
            $('.div-pontual').hide();
        }

    });

</script>
@endsection
