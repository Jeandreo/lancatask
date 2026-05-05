@extends('layouts.app')

@section('Page Title', 'Carteiras')

@section('content')
<div class="card">
    <div class="card-body">
        <table id="datatables-wallets" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
            <thead>
                <tr class="fw-bold fs-6 text-gray-800 px-7">
                    <th>Nome</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody class="table-pd">
                @foreach($contents as $content)
                <tr>
                    <td><a href="{{ route('financial.wallets.edit', $content->id) }}" class="text-gray-700 text-hover-primary fw-bold fs-6">{{ $content->name }}</a></td>
                    <td>{!! $content->status ? '<span class="badge badge-light-success">Ativo</span>' : '<span class="badge badge-light-danger">Inativo</span>' !!}</td>
                    <td>
                        <div class="d-flex align-items-center icons-table">
                            <a href="{{ route('financial.wallets.edit', $content->id) }}"><i class="fas fa-edit" title="Editar"></i></a>
                            <a href="{{ route('financial.wallets.destroy', $content->id) }}">{!! $content->status ? '<i class="fas fa-times-circle" title="Desativar"></i>' : '<i class="fas fa-redo" title="Reativar"></i>' !!}</a>
                            <a href="#" class="js-confirm-delete" data-url="{{ route('financial.wallets.delete', $content->id) }}" data-label="{{ $content->name }}" data-entity="carteira"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5 d-flex gap-2">
    <a href="{{ route('financial.index') }}"><label class="btn btn-light btn-sm text-uppercase fw-bolder">Voltar</label></a>
    <a href="{{ route('financial.wallets.create') }}"><label class="btn btn-info btn-active-success btn-sm text-uppercase fw-bolder">Adicionar</label></a>
</div>
@endsection

@section('custom-footer')
@parent
<script>
    $('#datatables-wallets').DataTable({
        order: [[1, 'desc'], [0, 'asc']],
        pageLength: 25,
        columnDefs: [
            { targets: 2, orderable: false, searchable: false }
        ]
    });
</script>
@endsection
