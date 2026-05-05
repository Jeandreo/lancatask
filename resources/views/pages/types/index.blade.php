@extends('layouts.app')

@section('Page Title', 'Tipos de Projetos')

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-pd"></tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">
        <a href="{{ route('projects.types.create') }}">
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
        ajax: "{{ route('projects.types.processing') }}",
        order: [[0, 'asc']],
        columns: [
            { data: 'name' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
</script>
@endsection
