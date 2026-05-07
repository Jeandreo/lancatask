@extends('layouts.app')

@section('Page Title', 'Financeiro')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end" id="section-filters">
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">Tipo</label>
                <select id="type" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Todos">
                    <option value=""></option>
                    <option value="entrada">Entrada</option>
                    <option value="debito">Débito</option>
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">Carteira</label>
                <select id="wallet_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Todas">
                    <option value=""></option>
                    @foreach($wallets as $wallet)
                    <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">Categoria</label>
                <select id="category_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Todas">
                    <option value=""></option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">Status da cobrança</label>
                <select id="billing_status" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Todos">
                    <option value=""></option>
                    <option value="pendente">Pendente</option>
                    <option value="pago">Pago</option>
                    <option value="vencido">Vencido</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-0 pt-6 justify-content-center">
        <div class="d-flex align-items-center gap-3">
            <button type="button" class="btn btn-icon btn-light-primary btn-sm" id="btnMonthPrev">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h3 class="card-title fw-bold text-gray-900 mb-0" id="currentMonthLabel"></h3>
            <button type="button" class="btn btn-icon btn-light-primary btn-sm" id="btnMonthNext">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <div class="card-body pt-0">
        <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
            <thead>
                <tr class="fw-bold fs-6 text-gray-800 px-7">
                    <th>Vencimento</th>
                    <th>Nome</th>
                    <th>Pessoa/Empresa</th>
                    <th>Status</th>
                    <th>Carteira</th>
                    <th>Categoria</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody class="table-pd"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="financialIncomeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Adicionar receita</h3>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"></i>
                </button>
            </div>
            <form id="financialIncomeForm" action="{{ route('financial.store') }}" method="POST">
                @csrf
                <div id="financialIncomeMethod"></div>
                <div class="modal-body">
                    @php($content = null)
                    @php($formIdSuffix = 'income')
                    @php($renderCounterpartyScript = false)
                    @php($fixedType = 'entrada')
                    @include('pages.financial._form')
                </div>
                <div class="modal-footer">
                    <span class="text-gray-600 fw-medium me-auto d-none" id="financialIncomeCreatedAtLabel"></span>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info btn-active-success" id="financialIncomeSubmit">Cadastrar receita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="financialExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Adicionar despesa</h3>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"></i>
                </button>
            </div>
            <form id="financialExpenseForm" action="{{ route('financial.store') }}" method="POST">
                @csrf
                <div id="financialExpenseMethod"></div>
                <div class="modal-body">
                    @php($content = null)
                    @php($formIdSuffix = 'expense')
                    @php($renderCounterpartyScript = false)
                    @php($fixedType = 'debito')
                    @include('pages.financial._form')
                </div>
                <div class="modal-footer">
                    <span class="text-gray-600 fw-medium me-auto d-none" id="financialExpenseCreatedAtLabel"></span>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger btn-active-danger" id="financialExpenseSubmit">Cadastrar despesa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-5 d-flex gap-2 flex-wrap">
    <button type="button" class="btn btn-success btn-sm text-uppercase fw-bolder" id="btnAddFinancialIncome">Adicionar receita</button>
    <button type="button" class="btn btn-danger btn-sm text-uppercase fw-bolder" id="btnAddFinancialExpense">Adicionar despesa</button>
</div>
@endsection

@section('custom-footer')
@parent
<script>
    let selectedMonth = new Date();
    selectedMonth.setDate(1);

    function getMonthFilter() {
        const year = selectedMonth.getFullYear();
        const month = String(selectedMonth.getMonth() + 1).padStart(2, '0');
        return year + '-' + month;
    }

    function renderMonthLabel() {
        const monthLabel = selectedMonth.toLocaleDateString('pt-BR', {
            month: 'long',
            year: 'numeric'
        });

        $('#currentMonthLabel').text(monthLabel.charAt(0).toUpperCase() + monthLabel.slice(1));
    }

    var table = $('#datatables').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: "{{ route('financial.processing') }}",
            data: function(data) {
                data.filter_month = getMonthFilter();
                data.type = $('#type').val();
                data.wallet_id = $('#wallet_id').val();
                data.category_id = $('#category_id').val();
                data.billing_status = $('#billing_status').val();
            }
        },
        order: [[0, 'asc']],
        columns: [
            { data: 'due_date' },
            { data: 'name' },
            { data: 'counterparty_name' },
            { data: 'billing_status' },
            { data: 'wallet_name' },
            { data: 'category_name' },
            { data: 'amount' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#section-filters select').on('change', function() {
        table.ajax.reload();
    });

    $('#btnMonthPrev').on('click', function () {
        selectedMonth.setMonth(selectedMonth.getMonth() - 1);
        renderMonthLabel();
        table.ajax.reload();
    });

    $('#btnMonthNext').on('click', function () {
        selectedMonth.setMonth(selectedMonth.getMonth() + 1);
        renderMonthLabel();
        table.ajax.reload();
    });

    renderMonthLabel();

    const incomeModal = new bootstrap.Modal(document.getElementById('financialIncomeModal'));
    const expenseModal = new bootstrap.Modal(document.getElementById('financialExpenseModal'));

    function loadCounterpartyOptions(type, selected, fieldId) {
        const field = $('#' + fieldId);
        field.html('<option value=""></option>').trigger('change.select2');
        if (!type) return;

        $.get("{{ route('financial.counterparties', '__type__') }}".replace('__type__', type), function (items) {
            items.forEach(function (item) {
                const option = new Option(item.name, item.id, false, String(selected) === String(item.id));
                field.append(option);
            });
            field.trigger('change.select2');
        });
    }

    function filterCategoriesByType(formId, type) {
        const categoryField = $('#' + formId + ' select[name="category_id"]');
        const currentValue = categoryField.val();

        categoryField.find('option').each(function () {
            const optionType = $(this).data('type');
            if (!optionType) {
                $(this).prop('disabled', false).show();
                return;
            }

            const visible = optionType === type;
            $(this).prop('disabled', !visible);
            $(this).toggle(visible);
        });

        const currentOption = categoryField.find('option:selected');
        if (currentOption.length && currentOption.data('type') && currentOption.data('type') !== type) {
            categoryField.val('');
        }

        categoryField.trigger('change.select2');
    }

    function resetModal(formId, methodId, typeId, counterpartyTypeId, counterpartyIdId, actionUrl) {
        const form = $('#' + formId);
        form.trigger('reset');
        form.attr('action', actionUrl || "{{ route('financial.store') }}");
        $('#' + methodId).html('');
        const type = $('#' + typeId).val();
        filterCategoriesByType(formId, type);
        $('#' + counterpartyTypeId).val('').trigger('change');
        loadCounterpartyOptions('', null, counterpartyIdId);

        const createdAtLabel = formId === 'financialIncomeForm'
            ? $('#financialIncomeCreatedAtLabel')
            : $('#financialExpenseCreatedAtLabel');

        createdAtLabel.text('').addClass('d-none');
    }

    function setCreatedAtLabel(formId, createdAt) {
        const createdAtLabel = formId === 'financialIncomeForm'
            ? $('#financialIncomeCreatedAtLabel')
            : $('#financialExpenseCreatedAtLabel');

        if (!createdAt) {
            createdAtLabel.text('').addClass('d-none');
            return;
        }

        createdAtLabel.text('Criada em: ' + createdAt).removeClass('d-none');
    }

    function bindCounterparty(typeId, fieldId) {
        $('#' + typeId).on('change', function () {
            loadCounterpartyOptions($(this).val(), null, fieldId);
        });
        loadCounterpartyOptions($('#' + typeId).val(), null, fieldId);
    }

    bindCounterparty('counterparty_type_income', 'counterparty_id_income');
    bindCounterparty('counterparty_type_expense', 'counterparty_id_expense');

    $('#btnAddFinancialIncome').on('click', function () {
        resetModal('financialIncomeForm', 'financialIncomeMethod', 'type_income', 'counterparty_type_income', 'counterparty_id_income');
        $('#financialIncomeSubmit').text('Cadastrar receita');
        incomeModal.show();
    });

    $('#btnAddFinancialExpense').on('click', function () {
        resetModal('financialExpenseForm', 'financialExpenseMethod', 'type_expense', 'counterparty_type_expense', 'counterparty_id_expense');
        $('#financialExpenseSubmit').text('Cadastrar despesa');
        expenseModal.show();
    });

    $(document).on('click', '.js-financial-edit', function (e) {
        e.preventDefault();
        const url = $(this).data('url');
        const updateUrl = $(this).data('update-url');

        $.get(url, { json: 1 }, function (response) {
            const isIncome = response.type === 'entrada';
            const formId = isIncome ? 'financialIncomeForm' : 'financialExpenseForm';
            const methodId = isIncome ? 'financialIncomeMethod' : 'financialExpenseMethod';
            const suffix = isIncome ? 'income' : 'expense';
            const modal = isIncome ? incomeModal : expenseModal;
            const submitId = isIncome ? '#financialIncomeSubmit' : '#financialExpenseSubmit';

            resetModal(formId, methodId, 'type_' + suffix, 'counterparty_type_' + suffix, 'counterparty_id_' + suffix, updateUrl);
            $('#' + methodId).html('@method("PUT")');
            $(submitId).text('Atualizar ' + (isIncome ? 'receita' : 'despesa'));

            $('#' + formId + ' input[name="name"]').val(response.name || '');
            $('#' + formId + ' input[name="date"]').val(response.date || '');
            $('#' + formId + ' input[name="amount"]').val(response.amount || '');
            $('#' + formId + ' select[name="wallet_id"]').val(response.wallet_id || '').trigger('change');
            $('#' + formId + ' select[name="category_id"]').val(response.category_id || '').trigger('change');
            filterCategoriesByType(formId, response.type);
            $('#' + formId + ' textarea[name="description"]').val(response.description || '');
            $('#counterparty_type_' + suffix).val(response.counterparty_type || '').trigger('change');
            loadCounterpartyOptions(response.counterparty_type || '', response.counterparty_id || null, 'counterparty_id_' + suffix);
            setCreatedAtLabel(formId, response.created_at || '');

            modal.show();
        });
    });

    $(document).on('click', '.js-financial-materialize', function (e) {
        e.preventDefault();

        const clientContractId = $(this).data('client-contract-id');
        const referencePeriod = $(this).data('reference-period');

        if (!clientContractId || !referencePeriod) {
            return;
        }

        $.post("{{ route('financial.materialize.projected') }}", {
            _token: "{{ csrf_token() }}",
            client_contract_id: clientContractId,
            reference_period: referencePeriod
        }).done(function () {
            table.ajax.reload();
        }).fail(function (xhr) {
            let message = 'Não foi possível materializar a cobrança projetada.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            alert(message);
        });
    });

    @if ($errors->any())
        @if(old('type') === 'debito')
            expenseModal.show();
        @else
            incomeModal.show();
        @endif
    @endif
</script>
@endsection
