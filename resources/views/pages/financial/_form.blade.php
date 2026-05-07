@php
    $formIdSuffix = isset($formIdSuffix) && $formIdSuffix !== '' ? (string) $formIdSuffix : '';
    $typeId = 'type' . ($formIdSuffix !== '' ? '_' . $formIdSuffix : '');
    $counterpartyTypeId = 'counterparty_type' . ($formIdSuffix !== '' ? '_' . $formIdSuffix : '');
    $counterpartyIdId = 'counterparty_id' . ($formIdSuffix !== '' ? '_' . $formIdSuffix : '');
    $renderCounterpartyScript = $renderCounterpartyScript ?? true;
    $fixedType = $fixedType ?? null;
    $resolvedType = in_array($fixedType, ['entrada', 'debito'], true)
        ? $fixedType
        : old('type', $content->type ?? 'entrada');
    $resolvedOriginType = old('origin_type', $content->origin_type ?? 'avulsa');
    $resolvedBillingStatus = old('billing_status', $content->billing_status ?? 'pendente');
@endphp

<div class="row">
    <input type="hidden" name="type" id="{{ $typeId }}" value="{{ $resolvedType }}">
    <input type="hidden" name="origin_type" value="{{ $resolvedOriginType }}">
    <input type="hidden" name="billing_status" value="{{ $resolvedBillingStatus }}">
    <div class="col-12 mb-4">
        <label class="required form-label fw-bold">Nome da transação:</label>
        <input type="text" class="form-control form-control-solid" name="name" value="{{ $content->name ?? old('name') }}" required>
    </div>
    <div class="col-md-4 mb-4">
        <label class="required form-label fw-bold">Data:</label>
        <input type="date" class="form-control form-control-solid" name="date" value="{{ old('date', isset($content) ? optional($content->date)->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-4 mb-4">
        <label class="required form-label fw-bold">Valor:</label>
        <input type="text" class="form-control form-control-solid input-money" name="amount" value="{{ $content->amount ?? old('amount') }}" required>
    </div>
    <div class="col-md-4 mb-4">
        <label class="form-label fw-bold">Vencimento:</label>
        <input type="date" class="form-control form-control-solid" name="due_date" value="{{ old('due_date', isset($content) ? optional($content->due_date)->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-4 mb-4">
        <label class="required form-label fw-bold">Carteira:</label>
        <select class="form-select form-select-solid" name="wallet_id" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            @foreach($wallets as $wallet)
                <option value="{{ $wallet->id }}" @selected(old('wallet_id', $content->wallet_id ?? '') == $wallet->id)>{{ $wallet->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-4">
        <label class="required form-label fw-bold">Categoria:</label>
        <select class="form-select form-select-solid" name="category_id" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" data-type="{{ $category->type }}" @selected(old('category_id', $content->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-4">
        <label class="form-label fw-bold">Favorecido é:</label>
        <select class="form-select form-select-solid" name="counterparty_type" id="{{ $counterpartyTypeId }}" data-control="select2" data-allow-clear="true" data-placeholder="Opcional">
            <option value=""></option>
            <option value="client" @selected(old('counterparty_type', $content->counterparty_type ?? '') === 'client')>Cliente</option>
            <option value="user" @selected(old('counterparty_type', $content->counterparty_type ?? '') === 'user')>Membro</option>
        </select>
    </div>
    <div class="col-md-4 mb-4">
        <label class="form-label fw-bold">Favorecido:</label>
        <select class="form-select form-select-solid" name="counterparty_id" id="{{ $counterpartyIdId }}" data-control="select2" data-allow-clear="true" data-placeholder="Selecione"></select>
    </div>

    <div class="col-12 mb-4">
        <label class="form-label fw-bold">Descrição:</label>
        <textarea class="form-control form-control-solid" rows="3" name="description">{{ $content->description ?? old('description') }}</textarea>
    </div>
</div>

@section('custom-footer')
@parent
@if($renderCounterpartyScript)
<script>
    function loadCounterpartyOptions(type, selected, fieldId = 'counterparty_id') {
        const field = $('#' + fieldId);
        field.html('<option value=""></option>').trigger('change.select2');

        if (!type) {
            return;
        }

        $.get("{{ route('financial.counterparties', '__type__') }}".replace('__type__', type), function (items) {
            items.forEach(function (item) {
                const option = new Option(item.name, item.id, false, String(selected) === String(item.id));
                field.append(option);
            });
            field.trigger('change.select2');
        });
    }

    $(document).ready(function () {
        const selectedType = $('#{{ $counterpartyTypeId }}').val();
        const selectedCounterparty = @json(old('counterparty_id', $content->counterparty_id ?? null));
        loadCounterpartyOptions(selectedType, selectedCounterparty, '{{ $counterpartyIdId }}');

        $('#{{ $counterpartyTypeId }}').on('change', function () {
            loadCounterpartyOptions($(this).val(), null, '{{ $counterpartyIdId }}');
        });
    });
</script>
@endif
@endsection
