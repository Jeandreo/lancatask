@extends('layouts.app')

@section('Page Title', 'Adicionar Projeto')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('pages.projects._form')
            <div class="d-flex justify-content-between">
                <a href="{{ route('projects.index') }}" class="btn btn-light mt-2">Voltar</a>
                <button type="submit" class="btn btn-primary btn-active-success mt-2">Cadastrar</button>
            </div>
        </form>
    </div>
</div>
@endsection
