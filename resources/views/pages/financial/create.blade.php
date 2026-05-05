@extends('layouts.app')

@section('Page Title', 'Adicionar Transação')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('financial.store') }}" method="POST">
                    @csrf
                    @include('pages.financial._form')
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('financial.index') }}" class="btn btn-light mt-2">Voltar</a>
                        <button type="submit" class="btn btn-info btn-active-success mt-2">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
