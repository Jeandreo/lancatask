@extends('layouts.app')

@section('Page Title', 'Minha Conta')

@section('content')
<form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-6 mb-5">
                    <label class="required form-label fw-bold">Nome:</label>
                    <input type="text" class="form-control form-control-solid" placeholder="Nome" name="name" value="{{ Auth::user()->name ?? old('name') }}" required/>
                </div>
                <div class="col-6 mb-5">
                    <label class="required form-label fw-bold">Email:</label>
                    <input type="email" class="form-control form-control-solid" placeholder="nome@gmail.com" name="email" value="{{ Auth::user()->email ?? old('email') }}" required/>
                </div>
                <div class="col-6 mb-5">
                    <label class="form-label fw-bold">Senha:</label>
                    <input type="password" class="form-control form-control-solid" placeholder="****" name="password" value=""/>
                </div>
                <div class="col-6 mb-5">
                    <label class="form-label fw-bold">Foto de Perfil:</label>
                    <input type="file" class="form-control form-control-solid" name="photo" accept="image/*"/>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between">
        <a href="{{ route('dashboard.index') }}" class="btn btn-light mt-2">Voltar</a>
        <button type="submit" class="btn btn-info btn-active-success mt-2">Atualizar</button>
    </div>
</form>
@endsection
