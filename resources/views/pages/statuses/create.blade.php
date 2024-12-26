@extends('layouts.app')

@section('Page Title', 'Adicionar Status')

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form action="{{ route('statuses.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						@include('pages.statuses._form')
						<div class="d-flex justify-content-between">
							<a href="{{ route('projects.index') }}" class="btn btn-light mt-2">Voltar</a>
							<button type="submit" class="btn btn-primary btn-active-success mt-2">Cadastrar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection
