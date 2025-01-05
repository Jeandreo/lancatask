<div class="row py-2">
    <div class="col-2">
        <label class="form-label fs-6 fw-bold text-gray-700 mb-3 required">Título/Cor:</label>
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
        <label class="form-label fs-6 fw-bold text-gray-700 mb-3">Descrição:</label>
    </div>
    <div class="col-10">
        <textarea class="form-control form-control-solid" name="description" cols="30" rows="3" placeholder="Sobre oque será o evento?">@if(isset($content)){{ $content->description }}@endif</textarea>
    </div>
</div>
<div class="row py-2 div-pontual">
    <div class="col-2">
        <label class="form-label fs-6 fw-bold text-gray-700">Início/Fim: </label>
    </div>
    <div class="col-5">
        <input class="form-control form-control-solid flatpickr-with-time cursor-pointer text-center" placeholder="00/00/0000 00:00" type="text" name="date_start" value="@if(isset($content)){{ "$content->date_start $content->hour_start" }}@endif" required>
    </div>
    <div class="col-5">
        <input class="form-control form-control-solid flatpickr-with-time cursor-pointer text-center" placeholder="00/00/0000 00:00" type="text" name="date_end" value="@if(isset($content)){{ "$content->date_end $content->hour_end" }}@endif" required>
    </div>
</div>

@section('custom-footer')
@parent
<script>
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

    $(document).ready(function(){
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
