@extends('layouts.app')

@section('Page Title', 'Tarefas')

@section('custom-head')
<script src="{{ asset('assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle datatables no-footer">
            <thead>
                <tr class="fw-bold fs-6 text-gray-800 px-7">
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Quando</th>
                    <th>Concluída</th>
                    <th>Projeto > Módulo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody class="table-pd">
                @foreach ($contents as $content)
                    <tr>
                        <td>
                            <span class="text-gray-700 text-hover-primary fw-bold fs-6 show-task cursor-pointer" data-task="{{ $content->id }}">
                                {{ $content->id }}
                            </span>
                        </td>
                        <td>
                            {{-- <div class="d-flex align-items-center">
                                <div class="symbol symbol-25px symbol-circle me-2" data-bs-toggle="tooltip" data-bs-original-title="{{ $content->name }}">
                                    <img alt="Pic" src="{{ findImage('users/photos/' . $content->designated_id . '.jpg') }}" class="object-fit-cover">
                                </div>
                                <span class="text-gray-700 text-hover-primary fw-bold fs-6 show-task cursor-pointer" data-task="{{ $content->id }}">
                                    {{ $content->name }}
                                </span>
                            </div> --}}
                        </td>
                        <td>
                            @if ($content->date)
                                <span class="text-gray-600">{{ $content->date->format('d/m/Y') }}</span>
                            @else
                                <span class="badge badge-light">Sem data</span>
                            @endif
                        </td>
                        <td>
                            @if ($content->checked == 1)
                                <span class="badge badge-light-success">Concluída</span>
                            @else
                                <span class="badge badge-light-danger">Não concluída</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $content->module->project_id) }}" class="text-gray-700 text-hover-primary fw-bolder">
                                {{ $content->module->project->name }} >
                                <span class="text-gray-600 fw-normal">
                                    {{ Str::limit($content->module->name, 25) }}
                                </span>
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
                                @if ($content->module->status == 1)
                                <a href="{{ route('tasks.destroy', $content->id) }}">
                                    @if ($content->status == 1)
                                    <i class="fas fa-times-circle" title="Desativar"></i>
                                    @else
                                    <i class="fas fa-redo" title="Reativar"></i>
                                    @endif
                                </a>
                                @else
                                <span class="badge badge-light" data-bs-toggle="tooltip" data-bs-original-title="Esta tarefa esta em um módulo desativado.">
                                    -
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" data-bs-focus="false" id="modal_task">
    <div class="modal-dialog modal-dialog-centered rounded">
        <div class="modal-content rounded bg-transparent" id="load-task">
            {{-- LOAP TASK HERE --}}
            {{-- LOAP TASK HERE --}}
            {{-- LOAP TASK HERE --}}
        </div>
    </div>
</div>
@endsection

@section('custom-footer')
@parent
@include('pages.tasks._javascript')
@endsection
