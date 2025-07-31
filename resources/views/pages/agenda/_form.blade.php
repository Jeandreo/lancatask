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
                <input type="color" class="form-control form-control-solid form-control-color" style="width: 100%" name="color" @if(isset($content)) value="{{ $content->color ?? old('color') }}" @else value="{{ randomColor() }}" @endif title="Escolha sua cor" required>
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
        <label class="form-label fs-6 fw-bold text-gray-900">Início/Fim: </label>
    </div>
    <div class="col-5">
        <input class="form-control form-control-solid flatpickr-with-time cursor-pointer text-center" placeholder="00/00/0000 00:00" type="text" name="date_start" value="@if(isset($content)){{ "$content->date_start $content->hour_start" }}@endif" required>
    </div>
    <div class="col-5">
        <input class="form-control form-control-solid flatpickr-with-time cursor-pointer text-center" placeholder="00/00/0000 00:00" type="text" name="date_end" value="@if(isset($content)){{ "$content->date_end $content->hour_end" }}@endif" required>
    </div>
</div>

@if (!isset($content))
<div class="row py-2">
    <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
        <label class="form-check-label form-label fs-6 fw-bold text-gray-900 mb-0 me-6" for="googleCalendar">
            Incluir no Google Calendar
        </label>
        <input class="form-check-input cursor-pointer" type="checkbox" value="1" checked id="googleCalendar" name="send_google" @if(isset($content) && $content->id_google) checked @endif/>
    </div>
</div>
<div class="row py-2 select-members">
    <div class="col-2">
        <label class="form-label fs-6 fw-bold text-gray-900 mb-3 required">Membros do time:</label>
    </div>
    <div class="col-10">
        <select class="form-select form-select-solid" name="users[]" multiple data-control="select2" data-placeholder="Selecione" required>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row py-2 select-clients">
    <div class="col-2">
        <label class="form-label fs-6 fw-bold text-gray-900 mb-3 required">Clientes:</label>
    </div>
    <div class="col-10">
        <select class="form-select form-select-solid" name="clients[]" multiple data-control="select2" data-placeholder="Selecione" required>
            @foreach($clients as $client)
                <option value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
        </select>
    </div>
</div>

@endif


@section('custom-footer')
@parent
<script>

function toggleParticipantsRequirement(checked) {
    // mostra / esconde
    $('.select-members, .select-clients')[ checked ? 'show' : 'hide' ]();
    // adiciona ou remove o required nos <select>
    $('.select-members select, .select-clients select')
      .prop('required', checked);
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
