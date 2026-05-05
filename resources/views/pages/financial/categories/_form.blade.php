<div class="row">
    <div class="col-md-8 mb-4">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" name="name" value="{{ $content->name ?? old('name') }}" required>
    </div>
    <div class="col-md-4 mb-4">
        <label class="required form-label fw-bold">Tipo:</label>
        <select class="form-select form-select-solid" name="type" data-control="select2" data-hide-search="true" data-placeholder="Selecione" required>
            <option value=""></option>
            <option value="entrada" @selected(old('type', $content->type ?? '') === 'entrada')>Receita</option>
            <option value="debito" @selected(old('type', $content->type ?? '') === 'debito')>Despesa</option>
        </select>
    </div>
</div>
