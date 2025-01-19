@extends('layouts.app')

@section('Page Title', 'Editar Usuário')

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form action="{{ route('users.update', $content->id) }}" method="POST" enctype="multipart/form-data">
						@csrf
						@method('PUT')
						@include('pages.users._form')
						<div class="d-flex justify-content-between">
							<a href="{{ route('users.index') }}" class="btn btn-light mt-2">Voltar</a>
							<button type="submit" class="btn btn-info btn-active-success mt-2">Atualizar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection
