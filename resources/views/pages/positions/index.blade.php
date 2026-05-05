@extends('layouts.app')

@section('Page Title', 'Cargos')

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Usuários</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-pd"></tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">
        <a href="{{ route('positions.create') }}">
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
        ajax: "{{ route('positions.processing') }}",
        order: [[0, 'asc']],
        columns: [
            { data: 'name' },
            { data: 'users' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
</script>
@endsection
