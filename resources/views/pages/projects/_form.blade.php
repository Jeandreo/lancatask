<div class="row">
    <div class="col mb-4">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome do projeto" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col mb-4">
        <label class="form-label fw-bold required">Tipo:</label>
        <select class="form-select form-select-solid" name="type_is" data-control="select2" data-hide-search="true" data-placeholder="Selecione" required>
            <option value=""></option>
            <option value="time" @if(isset($content) && $content->type_is == 'time' || !isset($content)) selected @endif>Time</option>
            <option value="pessoal" @if(isset($content) && $content->type_is == 'pessoal') selected @endif>Pessoal</option>
        </select>
    </div>
    <div class="col mb-4">
        <label class="form-label fw-bold required">Grupo:</label>
        <select class="form-select form-select-solid" name="type_id" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            @foreach ($types as $type)
            <option value="{{ $type->id }}" @if(isset($content) && $type->id == $content->type_id) selected @endif>{{  $type->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col mb-4 div-team" @if(isset($content) && $content->type_is == 'pessoal') style="display: none;" @endif>
        <label class="required form-label fw-bold">Time:</label>
        <select class="form-select form-select-solid select-with-images" name="team[]" data-control="select2" data-hide-search="true" data-placeholder="Selecione" multiple required>
            <option value=""></option>
            @foreach ($users as $user)
            <option value="{{ $user->id }}" @if(isset($content) && in_array($user->id, $content->users->pluck('id')->toArray()) || $user->id == Auth::id()) selected @endif data-kt-select2-user="{{ findImage('users/photos/' . $user->id . '.jpg') }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 mb-4p">
        <label class="form-label fw-bold">Descrição:</label>
        <textarea name="description" class="form-control form-control-solid" placeholder="Alguma observação sobre?">@if(isset($content->description)){{$content->description}}@endif</textarea>
    </div>
</div>


@section('custom-footer')
@parent
<script>
    $(document).ready(function () {
        $('[name="type_is"]').change(function(){

            var value = $(this).val();

            if(value == 'time'){
                $('.div-team').show();
                $('.div-team select').prop('required', true);
            } else {
                $('.div-team').hide();
                $('.div-team select').prop('required', false);
            }

        });
    });
</script>
@endsection

