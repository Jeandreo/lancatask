@extends('layouts.app')

@section('Page Title', 'Projetos')

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle datatables no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Time</th>
                        <th>Tarefas</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-pd">
                    @foreach ($contents as $content)
                        <tr>
                            <td>
                                <a href="{{ route('projects.show', $content->id) }}"
                                   class="text-gray-700 fw-bold text-hover-primary fs-6">
                                    {{ $content->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-light-primary">
                                    {{ $content->type->name }}
                                </span>
                            </td>
                            <td>
                                <div class="symbol-group symbol-hover flex-nowrap">
                                @foreach ($content->users as $user)
                                <div class="symbol symbol-30px symbol-circle" data-bs-toggle="tooltip" data-bs-original-title="{{ $user->name }}">
                                    <img alt="Pic" src="{{ findImage('users/photos/' . $user->id . '.jpg') }}" class="object-fit-cover">
                                </div>
                                @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-primary">
                                    {{ $content->tasksCount('checked') }} / {{ $content->tasksCount() }}
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
                                    <a href="{{ route('projects.edit', $content->id) }}">
                                        <i class="fas fa-edit" title="Editar"></i>
                                    </a>
                                    @if ($content->status == 1)
                                        <a href="{{ route('projects.destroy', $content->id) }}">
                                            <i class="fas fa-times-circle" title="Desativar"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('projects.destroy', $content->id) }}">
                                            <i class="fas fa-redo" title="Reativar"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">
        <a href="{{ route('projects.create') }}">
            <label class="btn btn-primary btn-active-success btn-sm text-uppercase fw-bolder">Adicionar</label>
        </a>
    </div>
@endsection
