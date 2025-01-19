@extends('layouts.app')

@section('Page Title', 'Lançamentos')

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle datatables no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Time</th>
                        <th class="text-center px-0">Concluídas</th>
                        <th class="text-center px-0">Tarefas</th>
                        <th class="text-center px-0">Iníciado</th>
                        <th class="text-center pe-4 w-150px">Ações</th>
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
                                <div class="symbol-group symbol-hover flex-nowrap">
                                @foreach ($content->users as $user)
                                <div class="symbol symbol-30px symbol-circle" data-bs-toggle="tooltip" data-bs-original-title="{{ $user->name }}">
                                    <img alt="Pic" src="{{ findImage('users/photos/' . $user->id . '.jpg') }}" class="object-fit-cover">
                                </div>
                                @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-success">
                                    {{ $content->tasksCount('checked') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-primary">
                                    {{ $content->tasksCount() }}
                                </span>
                            </td>
                            <td class="text-center px-0">
                                <span class="badge badge-light">
                                    {{ $content->created_at->format('d/m/Y H:i') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('projects.show', $content->id) }}" class="btn btn-info btn-sm fw-bold text-uppercase py-2">
                                    Acessar Projeto
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
