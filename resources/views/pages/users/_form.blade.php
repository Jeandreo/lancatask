<div class="row">
    <div class="col-4 mb-5">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome do projeto" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold required">Grupo:</label>
        <select class="form-select form-select-solid" name="type" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            <option value="Administrador" @if(isset($content) && $content->type == 'Administrador') selected @endif>Administrador</option>
            <option value="Gerente" @if(isset($content) && $content->type == 'Time') selected @endif>Gerente</option>
            <option value="Colaborador" @if(isset($content) && $content->type == 'Time') selected @endif>Colaborador</option>
        </select>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold required">Grupo:</label>
        <select class="form-select form-select-solid" name="type" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            @foreach ($positions as $position)
            <option value="{{ $position->id }}" @if(isset($content) && $content->id == $position->id) selected @endif>{{ $position->name }}</option>
            @endforeach
        </select>
    </div>
</div>