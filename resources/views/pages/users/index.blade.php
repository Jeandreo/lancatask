@extends('layouts.app')

@section('Page Title', 'Usuários')

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Grupo</th>
                        <th>Cargo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-pd"></tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">
        <a href="{{ route('users.create') }}">
            <label class="btn btn-info btn-active-success btn-sm text-uppercase fw-bolder">Adicionar</label>
        </a>
    </div>
@endsection

@section('custom-footer')
@parent
<script>
    $('#datatables').DataTable({
        serverSide: true,
        processing: true,
        ajax: "{{ route('users.processing') }}",
        order: [[0, 'asc']],
        columns: [
            { data: 'name' },
            { data: 'group' },
            { data: 'position' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
</script>
@endsection
