<div class="row">
    <div class="col-6 mb-4">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome do projeto" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col-6 mb-4">
        <label class="form-label fw-bold required">Tipo:</label>
        <select class="form-select form-select-solid" name="type" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            <option value="Lançamento" @if(!isset($content) || $content->type == 'Lançamento') selected @endif>Lançamento</option>
            <option value="Time" @if(isset($content) && $content->type == 'Time') selected @endif>Time</option>
        </select>
    </div>
    <div class="col-6 mb-4">
        <label class="required form-label fw-bold">Cor:</label>
        <input type="color" class="form-control form-control-solid" placeholder="Selecione uma cor" name="color" value="{{ $content->color ?? '#009ef7' }}" required style="height: 43px;"/>
    </div>
    <div class="col-6 mb-4">
        <label class="required form-label fw-bold">Time:</label>
        <select class="form-select form-select-solid select-with-images" name="team[]" data-control="select2" data-hide-search="true" data-placeholder="Selecione" multiple>
            <option value=""></option>
            @foreach ($users as $user)
            <option value="{{ $user->id }}" @if(isset($content) && in_array($user->id, $content->users->pluck('id')->toArray())) selected @endif data-kt-select2-user="{{ findImage('users/photos/' . $user->id . '.jpg') }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 mb-4p">
        <label class="form-label fw-bold">Descrição:</label>
        <textarea name="description" class="form-control form-control-solid" placeholder="Alguma observação sobre?">@if(isset($content->description)){{$content->description}}@endif</textarea>
    </div>
</div>
