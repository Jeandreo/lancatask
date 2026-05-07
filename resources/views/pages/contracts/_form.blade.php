<div class="row">
    <div class="col-md-9 mb-4">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col-md-3 mb-4">
        <label class="form-label fw-bold d-block">&nbsp;</label>
        <input type="hidden" name="is_open_ended" value="0">
        <label class="form-check form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" name="is_open_ended" id="is_open_ended" value="1" @checked(old('is_open_ended', $content->is_open_ended ?? false))>
            <span class="form-check-label fw-bold">Contrato sem fim</span>
        </label>
    </div>
    <div class="col-md-3 mb-4" id="duration-wrapper">
        <label class="required form-label fw-bold">Duração (meses):</label>
        <input type="number" min="1" class="form-control form-control-solid" placeholder="Ex: 12" name="duration_in_months" id="duration_in_months" value="{{ $content->duration_in_months ?? old('duration_in_months', 12) }}" required>
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

@section('custom-footer')
@parent
<script>
    $(document).ready(function () {
        function toggleDuration() {
            const isOpenEnded = $('#is_open_ended').is(':checked');
            const field = $('#duration_in_months');

            if (isOpenEnded) {
                $('#duration-wrapper').hide();
                field.prop('required', false);
                return;
            }

            $('#duration-wrapper').show();
            field.prop('required', true);
        }

        $('#is_open_ended').on('change', toggleDuration);
        toggleDuration();
    });
</script>
@endsection
