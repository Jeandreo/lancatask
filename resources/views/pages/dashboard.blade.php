@extends('layouts.app')

@section('Page Title', 'Minhas Tarefas')

@section('custom-head')
<script src="{{ asset('assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header min-h-50px ps-2 pe-6">
                <div class="d-flex align-items-center ps-3 pe-5 w-50">
                    <p class="py-2 px-3 text-gray-700 fs-5 fw-bold p-0 module-title">Minhas tarefas</p>
                </div>
                <div class="d-none d-md-flex align-items-center">
                    <div class="w-125px text-center text-gray-700 fs-7 text-uppercase fw-bold">
                        Designado
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
                @foreach ($tasks as $task)
                    @include('pages.tasks._task', ['hideMove' => true])
                @endforeach
            </div>
        </div>
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
@include('pages.tasks._javascript')
@endsection
