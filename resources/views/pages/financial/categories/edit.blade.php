@extends('layouts.app')

@section('Page Title', 'Editar Categoria')

@section('content')
<div class="row"><div class="col-12"><div class="card"><div class="card-body">
<form action="{{ route('financial.categories.update', $content->id) }}" method="POST">
@csrf
@method('PUT')
@include('pages.financial.categories._form')
<div class="d-flex justify-content-between">
<a href="{{ route('financial.categories.index') }}" class="btn btn-light mt-2">Voltar</a>
<button type="submit" class="btn btn-info btn-active-success mt-2">Atualizar</button>
</div>
</form>
</div></div></div></div>
@endsection
