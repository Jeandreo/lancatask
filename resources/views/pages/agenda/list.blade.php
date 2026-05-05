@extends('layouts.app')

@section('Page Title', 'Eventos')

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-pd"></tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">
        <a href="{{ route('agenda.index') }}">
            <label class="btn btn-info btn-active-success btn-sm text-uppercase fw-bolder">Calendário</label>
        </a>
    </div>
@endsection

@section('custom-footer')
@parent
<script>
    $('#datatables').DataTable({
        serverSide: true,
        processing: true,
        ajax: "{{ route('agenda.processing') }}",
        order: [[0, 'desc']],
        columns: [
            { data: 'name' },
            { data: 'date' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
</script>
@endsection
