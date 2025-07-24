@extends('layouts.app')

@section('Page Title', 'Adicionar Cliente')

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						@include('pages.clients._form')
						<div class="d-flex justify-content-between">
							<a href="{{ route('clients.index') }}" class="btn btn-light mt-2">Voltar</a>
							<button type="submit" class="btn btn-info btn-active-success mt-2">Cadastrar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection
