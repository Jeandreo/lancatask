@extends('layouts.app')

@section('Page Title', 'Adicionar Contrato')

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						@include('pages.contracts._form')
						<div class="d-flex justify-content-between">
							<a href="{{ route('contracts.index') }}" class="btn btn-light mt-2">Voltar</a>
							<button type="submit" class="btn btn-info btn-active-success mt-2">Cadastrar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection
