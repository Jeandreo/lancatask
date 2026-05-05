@extends('layouts.app')

@section('Page Title', 'Categorias Financeiras')

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle no-footer">
            <thead>
                <tr class="fw-bold fs-6 text-gray-800 px-7">
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody class="table-pd">
                @foreach($contents as $content)
                <tr>
                    <td><a href="{{ route('financial.categories.edit', $content->id) }}" class="text-gray-700 text-hover-primary fw-bold fs-6">{{ $content->name }}</a></td>
                    <td>{!! $content->type === 'entrada' ? '<span class="badge badge-light-success">Receita</span>' : '<span class="badge badge-light-danger">Despesa</span>' !!}</td>
                    <td>{!! $content->status ? '<span class="badge badge-light-success">Ativo</span>' : '<span class="badge badge-light-danger">Inativo</span>' !!}</td>
                    <td>
                        <div class="d-flex align-items-center icons-table">
                            <a href="{{ route('financial.categories.edit', $content->id) }}"><i class="fas fa-edit" title="Editar"></i></a>
                            <a href="{{ route('financial.categories.destroy', $content->id) }}">{!! $content->status ? '<i class="fas fa-times-circle" title="Desativar"></i>' : '<i class="fas fa-redo" title="Reativar"></i>' !!}</a>
                            <a href="#" class="js-confirm-delete" data-url="{{ route('financial.categories.delete', $content->id) }}" data-label="{{ $content->name }}" data-entity="categoria"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>
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
    <a href="{{ route('financial.categories.create') }}"><label class="btn btn-info btn-active-success btn-sm text-uppercase fw-bolder">Adicionar</label></a>
</div>
@endsection
