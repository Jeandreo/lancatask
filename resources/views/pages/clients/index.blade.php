@extends('layouts.app')

@section('Page Title', 'Clientes')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form id="section-filters" class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-2">Status</label>
                    <select name="status" id="status" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Todos">
                        <option value="">Todos</option>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-2">Pessoa</label>
                    <select name="person_type" id="person_type" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Todos">
                        <option value="">Todos</option>
                        <option value="PF">PF</option>
                        <option value="PJ">PJ</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label mb-2">Contrato</label>
                    <select name="contract_id" id="contract_id" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Todos">
                        <option value="">Todos</option>
                        @foreach ($contracts as $contract)
                            <option value="{{ $contract->id }}">
                                {{ $contract->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="filter">Filtrar</button>
                    <button type="button" class="btn btn-light" id="clear-filter">Limpar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Tipo de Contrato</th>
                        <th>Valor</th>
                        <th>Entrada</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-pd"></tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">
        <a href="{{ route('clients.create') }}">
            <label class="btn btn-info btn-active-success btn-sm text-uppercase fw-bolder">Adicionar</label>
        </a>
    </div>
@endsection

@section('custom-footer')
@parent
<script>
    var table = $('#datatables').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: "{{ route('clients.processing') }}",
            data: function(data) {
                data.status = $('#status').val();
                data.person_type = $('#person_type').val();
                data.contract_id = $('#contract_id').val();
                data.order_by = data.columns[data.order[0].column].data;
            }
        },
        order: [[0, 'asc']],
        pageLength: 25,
        columns: [
            { data: 'name', orderable: true },
            { data: 'contract', orderable: true },
            { data: 'value', orderable: true },
            { data: 'start_date', orderable: true },
            { data: 'status', orderable: true },
            { data: 'actions', orderable: false }
        ],
        language: {
            search: 'Pesquisar:',
            lengthMenu: 'Mostrando _MENU_ registros por página',
            zeroRecords: 'Ops, não encontramos nenhum resultado :(',
            info: 'Mostrando _START_ até _END_ de _TOTAL_ registros',
            infoEmpty: 'Nenhum registro disponível',
            infoFiltered: '(Filtrando _MAX_ registros)',
            processing: 'Filtrando dados',
            paginate: {
                previous: 'Anterior',
                next: 'Próximo',
                first: '<i class="fa-solid fa-angles-left text-gray-700 text-hover-primary cursor-pointer"></i>',
                last: '<i class="fa-solid fa-angles-right text-gray-700 text-hover-primary cursor-pointer"></i>',
            }
        },
        dom: "<'row'" +
            "<'col-sm-6 d-flex align-items-center justify-conten-start'l>" +
            "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
            ">" +
            "<'table-responsive'tr>" +
            "<'row'" +
            "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
            "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
            ">"
    });

    $('#filter').on('click', function() {
        table.ajax.reload();
    });

    $('#clear-filter').on('click', function() {
        $('#status').val('');
        $('#person_type').val('');
        $('#contract_id').val('');
        $('#status, #person_type, #contract_id').trigger('change.select2');
        table.search('').ajax.reload();
    });

    $('#section-filters input, #section-filters select').on('change', function() {
        table.ajax.reload();
    });

    $(document).on('click', '.js-delete-client', function(e) {
        e.preventDefault();

        const url = $(this).data('url');
        const name = $(this).data('name');

        Swal.fire({
            text: `Tem certeza que deseja excluir o cliente ${name}? Esta ação não pode ser desfeita.`,
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn fw-bold btn-danger',
                cancelButton: 'btn fw-bold btn-active-light-primary',
            }
        }).then(function(result) {
            if (!result.isConfirmed) {
                return;
            }

            window.location.href = url;
        });
    });
</script>
@endsection
