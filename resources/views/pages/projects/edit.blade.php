@extends('layouts.app')

@section('Page Title', 'Editar Projeto')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('projects.update', $content->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('pages.projects._form')
            <div class="d-flex justify-content-between">
                <a href="{{ route('projects.index') }}" class="btn btn-light mt-2">Voltar</a>
                <button type="submit" class="btn btn-primary btn-active-success mt-2">Atualizar</button>
            </div>
        </form>
    </div>
</div>
@endsection
