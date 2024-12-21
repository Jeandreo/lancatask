@extends('layouts.app')

@section('title-page', 'Adicionar Categoria')

@section('title-toolbar', 'Adicionar Categoria')

@section('content')
	@include('layouts.title')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form action="{{ route('financial.categories.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						@include('pages.financial_categories._form')
						<div class="d-flex justify-content-between">
							<a href="{{ route('financial.categories.index') }}" class="btn btn-light mt-2">Voltar</a>
							<button type="submit" class="btn btn-primary btn-active-danger mt-2">Cadastrar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection