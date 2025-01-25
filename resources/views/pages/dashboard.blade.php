@extends('layouts.app')

@section('Page Title', 'Minhas Tarefas')

@section('custom-head')
<script src="{{ asset('assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if ($tasks->where('date_end', '<', now())->count())
        <div class="card mb-4">
            <div class="card-header min-h-50px ps-2 pe-6">
                <div class="d-flex align-items-center ps-3 pe-5 w-50">
                    <p class="py-2 px-3 text-gray-700 fs-5 fw-bold p-0 module-title">Tarefas Atrasadas</p>
                </div>
                <div class="d-none d-md-flex align-items-center">
                    <div class="w-125px text-center text-gray-700 fs-7 text-uppercase fw-bold">
                        Responsáveis
                    </div>
                    <div class="d-flex align-items-center justify-content-center cursor-pointer w-150px text-gray-700 fs-7 text-uppercase fw-bold">
                        Status
                    </div>
                    <div class="d-flex align-items-center justify-content-center cursor-pointer w-200px text-gray-700 fs-7 text-uppercase fw-bold">
                        Data
                    </div>
                </div>
            </div>
            <div class="card-body p-5">
                @foreach ($tasks->where('date_end', '<', now()) as $task)
                <div class="div-task" style="border-bottom: solid 2px white;">
                    @include('pages.tasks._task', ['hideMove' => true, 'showProject' => true])
                </div>
                @endforeach
            </div>
        </div>
        @endif
        <div class="card">
            <div class="card-header min-h-50px ps-2 pe-6">
                <div class="d-flex align-items-center ps-3 pe-5 w-50">
                    <p class="py-2 px-3 text-gray-700 fs-5 fw-bold p-0 module-title">Minhas tarefas</p>
                </div>
                <div class="d-none d-md-flex align-items-center">
                    <div class="w-125px text-center text-gray-700 fs-7 text-uppercase fw-bold">
                        Responsáveis
                    </div>
                    <div class="d-flex align-items-center justify-content-center cursor-pointer w-150px text-gray-700 fs-7 text-uppercase fw-bold">
                        Status
                    </div>
                    <div class="d-flex align-items-center justify-content-center cursor-pointer w-200px text-gray-700 fs-7 text-uppercase fw-bold">
                        Data
                    </div>
                </div>
            </div>
            <div class="card-body p-5">
                @if ($tasks->where('date_end', '>=', now())->count() || $tasks->where('date_end', null)->count())
                    @foreach ($tasks->where('date_end', null) as $task)
                    <div class="div-task">
                        @include('pages.tasks._task', ['hideMove' => true, 'showProject' => true])
                    </div>
                    @endforeach
                    @foreach ($tasks->where('date_end', '>=', now()) as $task)
                    <div class="div-task">
                        @include('pages.tasks._task', ['hideMove' => true, 'showProject' => true])
                    </div>
                    @endforeach
                @else
                    <div class="w-100 bg-light border border-dashed border-1 border-gray-200 h-125px rounded d-flex justify-content-center align-items-center">
                        <div class="d-flex">
                            <i class="fa-regular fa-face-smile-wink text-success fs-3x me-4"></i>
                            <div>
                                <h2 class="fw-bold text-gray-700 mb-0">Sem tarefas no dentro do prazo no momento</h2>
                                <p class="m-0 text-gray-600">Nenhuma tarefa atribuida a você no momento sem prazo ou dentro do prazo, volte em breve.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@include('pages.tasks._modals')
@endsection

@section('custom-footer')
    @parent
    @include('pages.tasks._javascript')
@endsection
