@extends('layouts.app')

@section('Page Title', 'Editar Projeto')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('projects.update', $content->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('pages.projects._form')
            <div class="d-flex justify-content-between">
                <a href="{{ route('projects.index') }}" class="btn btn-light mt-2">Voltar</a>
                <button type="submit" class="btn btn-info btn-active-success mt-2">Atualizar</button>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header min-h-50px">
        <p class="card-title text-gray-900 fs-5 fw-bold p-0">Status do projeto</p>
        <div class="card-toolbar">
            <a href="{{ route('statuses.create') }}">
                <label class="btn btn-info btn-active-success btn-sm fw-bold">Adicionar Novo Status</label>
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle datatables no-footer">
            <thead>
                <tr class="fw-bold fs-6 text-gray-800 px-7">
                    <th>Nome</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody class="table-pd">
                @foreach ($content->statuses as $content)
                    <tr>
                        <td>
                            <a href="{{ route('statuses.edit', $content->id) }}" class="text-gray-700 text-hover-primary fw-bold fs-6">
                                {{ $content->name }}
                            </a>
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
                                <a href="{{ route('statuses.edit', $content->id) }}">
                                    <i class="fas fa-edit" title="Editar"></i>
                                </a>
                                <a href="{{ route('statuses.destroy', $content->id) }}">
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
@endsection
