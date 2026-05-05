@extends('layouts.app')

@section('Page Title', 'Módulos')

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Projeto</th>
                        <th>Tarefas</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-pd"></tbody>
            </table>
        </div>
    </div>
@endsection

@section('custom-footer')
@parent
<script>
    $('#datatables').DataTable({
        serverSide: true,
        processing: true,
        ajax: "{{ route('modules.processing') }}",
        order: [[0, 'asc']],
        columns: [
            { data: 'name' },
            { data: 'project' },
            { data: 'tasks' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
</script>
@endsection
