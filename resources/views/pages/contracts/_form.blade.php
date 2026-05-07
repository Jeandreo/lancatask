<div class="row">
    <div class="col-md-9 mb-4">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col-md-3 mb-4">
        <label class="required form-label fw-bold">Duração (meses):</label>
        <input type="number" min="1" class="form-control form-control-solid" placeholder="Ex: 12" name="duration_in_months" value="{{ $content->duration_in_months ?? old('duration_in_months', 12) }}" required>
        <small class="text-muted">Define quantas transações serão geradas.</small>
    </div>
    <div class="col-md-6 mb-4">
        <label class="required form-label fw-bold">Carteira padrão:</label>
        <select class="form-select form-select-solid" name="wallet_id" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            @foreach($wallets as $wallet)
                <option value="{{ $wallet->id }}" @selected(old('wallet_id', $content->wallet_id ?? '') == $wallet->id)>{{ $wallet->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-4">
        <label class="required form-label fw-bold">Categoria padrão (entrada):</label>
        <select class="form-select form-select-solid" name="category_id" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $content->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
</div>
