@extends('layouts.app')

@section('Page Title', 'Cargos')

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle datatables no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Usuários</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-pd">
                    @foreach ($contents as $content)
                        <tr>
                            <td>
                                <a href="{{ route('positions.edit', $content->id) }}" class="text-gray-700 text-hover-primary fw-bold fs-6">
                                    {{ $content->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-light">
                                    {{ $content->users->count() }}
                                </span>
                            </td>
                            <td>
                                @if ($content->status == 1)
                                    <span class="badge badge-light-success">Ativo</span>
                                @else
                                    <span class="badge badge-light-danger">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center icons-table">
                                    <a href="{{ route('positions.edit', $content->id) }}">
                                        <i class="fas fa-edit" title="Editar"></i>
                                    </a>
                                    <a href="{{ route('positions.destroy', $content->id) }}">
                                        @if ($content->status == 1)
                                        <i class="fas fa-times-circle" title="Desativar"></i>
                                        @else
                                        <i class="fas fa-redo" title="Reativar"></i>
                                        @endif
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">
        <a href="{{ route('positions.create') }}">
            <label class="btn btn-primary btn-active-success btn-sm text-uppercase fw-bolder">Adicionar</label>
        </a>
    </div>
@endsection
