@extends('layouts.app')

@section('Page Title', 'Tarefas')

@section('custom-head')
<script src="{{ asset('assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
            <thead>
                <tr class="fw-bold fs-6 text-gray-800 px-7">
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Quando</th>
                    <th>Concluída</th>
                    <th>Projeto</th>
                    <th>Módulo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody class="table-pd">
                {{-- RESULTS HERE --}}
            </tbody>
        </table>
    </div>
</div>
@include('pages.tasks._modals')
@endsection

@section('custom-footer')
@parent
@include('pages.tasks._javascript')


<script>
    // COLUMNS
    var columnTable = [{
            data: "id",
            className: 'id',
            orderable: true,
            searchable: false
        },
        {
            data: "name",
            className: 'name',
            orderable: true,
            searchable: true
        },
        {
            data: "when",
            className: 'when',
            orderable: true,
            searchable: true
        },
        {
            data: "checked",
            className: 'checked',
            orderable: true,
            searchable: false
        },
        {
            data: "project",
            className: 'project',
            orderable: true,
            searchable: false
        },
        {
            data: "module",
            className: 'module',
            orderable: true,
            searchable: false
        },
        {
            data: "status",
            className: 'status',
            orderable: true,
            searchable: false
        },
        {
            data: "actions",
            className: 'actions',
            orderable: true,
            searchable: false
        },
    ];

    // URL TO PROCESSING
    var url = "{{ route('tasks.processing') }}";

    // PARAMETERS
    var parameters = {
        columns: columnTable,
        url: url,
    }

    // OPTIONS
    var options = {
        selector: '#datatables',
        items: 25,
        order: [
            [0, 'desc']
        ]
    }

    // SELECT TABLE
    const table = $(options['selector']);

    // CONFIG TABLE
    const dataTableOptions = {
        serverSide: true,
        ajax: {
            url: parameters['url'],
            data: function(data) {
                data.register = $('#register').val();
                data.agenda = $('#last-agenda').val();
                data.stages = $('#stage').val();
                data.funnels = $('#funnels').val();
                data.stores = $('#stores').val();
                data.origins = $('#origins').val();
                data.sellers = $('#sellers').val();
                data.campaigns = $('#campaigns').val();
                data.millestones = $('#millestones').val();
                data.searchBy = data.search.value;
                data.order_by = data.columns[data.order[0].column].data;
                data.per_page = data.length
            }
        },
        buttons: false,
        searching: true,
        order: options['order'],
        pageLength: options['items'],
        columns: parameters['columns'],
        "language": {
            "search": "Pesquisar:",
            "lengthMenu": "Mostrando _MENU_ registros por página",
            "zeroRecords": "Ops, não encontramos nenhum resultado :(",
            "info": "Mostrando _START_ até _END_ de _TOTAL_ registros",
            "infoEmpty": "Nenhum registro disponível",
            "infoFiltered": "(Filtrando _MAX_ registros)",
            "processing": "Filtrando dados",
            "paginate": {
                "previous": "Anterior",
                "next": "Próximo",
                "first": '<i class="fa-solid fa-angles-left text-gray-300 text-hover-primary cursor-pointer"></i>',
                "last": '<i class="fa-solid fa-angles-right text-gray-300 text-hover-primary cursor-pointer"></i>',
            }
        },
        "dom": "<'row'" +
            "<'col-sm-6 d-flex align-items-center justify-conten-start'l>" +
            "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
            ">" +

            "<'table-responsive'tr>" +

            "<'row'" +
            "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
            "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
            ">",
    };

    // Gera tabela
    table.DataTable(dataTableOptions);

    // // Ajusta o tooltip ao atualizar tabela
    // table.on('xhr.dt', function (e, settings, json) {
    //     $('body').tooltip({
    //         selector: '[data-bs-toggle="tooltip"]',
    //         html: true,
    //     });
    // });

    // // Filtrar dados
    // $('#section-bussiness input, #section-bussiness select').on('change', function() {
    //     table.DataTable().ajax.reload();
    // });

    // // Filtrar dados
    // $('#filtrar').on('click', function() {
    //     table.DataTable().ajax.reload();
    // });
</script>
@endsection
