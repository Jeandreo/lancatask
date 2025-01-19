@extends('layouts.app')

@section('Page Title', 'Eventos')

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatables" class="table table-dark-header table-striped table-row-bordered gy-2 gs-2 gx-0 border align-middle datatables no-footer">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                        <th>Nome</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-pd">
                    @foreach ($contents as $content)
                        <tr>
                            <td>
                                <span class="text-gray-700 fw-bold fs-6">
                                    {{ $content->name }}
                                </span>
                            </td>
                            <td>
                                @if ($content->date_start != $content->date_end)
                                <span class="fw-bold text-gray-700">
                                 {{ date('d/m/Y H:i', strtotime($content->date_start . ' ' . $content->hour_start)) }}
                                 até
                                 {{ date('d/m/Y H:i', strtotime($content->date_end . ' ' . $content->hour_end)) }}
                                </span>
                                @else
                                <span class="fw-bold text-gray-700">
                                 {{ date('d/m/Y', strtotime($content->date_start)) }} das {{ date('H:i', strtotime($content->hour_start)) }}
                                 até
                                 {{ date('H:i', strtotime($content->date_end . ' ' . $content->hour_end)) }}
                                </span>
                                @endif
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
                                    @if ($content->status == 1)
                                        <a href="{{ route('agenda.destroy', $content->id) }}">
                                            <i class="fas fa-times-circle" title="Desativar"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('agenda.destroy', $content->id) }}">
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
            <label class="btn btn-info btn-active-success btn-sm text-uppercase fw-bolder">Adicionar</label>
        </a>
    </div>

    <script>
        function confirmDelete(url) {
            if (confirm("Tem certeza de que deseja apagar este projeto permanentemente?")) {
                window.location.href = url;
            }
        }
    </script>
@endsection
