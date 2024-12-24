@extends('layouts.app')

@section('title-page', $type == 'ideias' ? 'Ideias' : 'Excluídas')

@section('title-toolbar', $type == 'ideias' ? 'Ideias' : 'Excluídas')

@section('custom-head')
<script src="{{ asset('assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
@endsection

@section('content')
@include('layouts.title')
<div class="app-main flex-column flex-row-fluid " id="kt_app_main">
	<div class="d-flex flex-column flex-column-fluid">                             
		<div id="kt_app_content" class="app-content  flex-column-fluid py-6" >
			<div id="kt_app_content_container" class="app-container  container-fluid ">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<table class="table table-striped table-row-bordered gy-3 gs-7 border rounded align-middle dmk-datatables">
									<thead>
										<tr class="fw-bold fs-6 text-gray-800 px-7">
											<th width="4%" class="pe-0 ps-5">ID</th>
											<th>Nome</th>
											<th>Projeto</th>
											<th>Status</th>
											<th>Designado</th>
											<th class="text-center">Status</th>
											<th class="text-center" width="160px">
												<span>Ações</span>
											</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($contents as $content)
										<tr>
											<td class="pe-0 ps-4">
												<span class="fw-normal">
													{{  str_pad($content->id , 4 , '0' , STR_PAD_LEFT)}}
												</span>
											</td>
											<td>
												<a href="{{ route('users.edit', $content->id) }}" class="d-flex align-items-center text-gray-700 text-hover-primary">
													{{ $content->name }}
												</a>
											</td>
											<td>
												<a href="{{ route('projects.show', $content->project_id) }}" class="d-flex align-items-center text-gray-700 text-hover-primary">
													{{ $content->project->name }}
												</a>
											</td>
											<td>
												<span class="badge py-2 fw-bold ms-2 fs-8 py-1 px-3" style="background: {{ hex2rgb($content->statusInfo->color, 15) }}; color: {{ $content->statusInfo->color, 100 }}">{{ $content->statusInfo->name }}</span>
											</td>
											<td>
												<a class="d-flex align-items-center text-gray-700 text-hover-primary">
													<div class="symbol symbol-25px symbol-circle me-2">
														<img alt="Pic" src="{{ findImage('users/' . $content->designated->id . '/' . 'perfil-35px.jpg') }}">
													</div>
													{{ $content->designated->name }}
												</a>
											</td>
											<td class="text-center">
												@if($content->status == 2) 
												<span class="badge badge-light-light">
													Stand By
												</span>
												@elseif($content->status == 0) 
												<span class="badge badge-light-danger">
													Excluída
												</span>
												@endif
											</td>
											<td class="text-center">
												@if ($content->status == 2)
												<a href="{{ route('tasks.stand.by.active', $content->id) }}" class="btn btn-sm btn-primary btn-active-success text-uppercase">
													Ativar Tarefa
												</a>
												@else
												<a href="{{ route('tasks.destroy', $content->id) }}" class="btn btn-sm btn-primary btn-active-success text-uppercase">
													Ativar Tarefa
												</a>
												@endif
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
						<div class="d-flex justify-content-between mt-6">
							<a href="{{ route('dashboard.index') }}" class="btn btn-sm fw-bold btn-secondary">Voltar</a>
							@if ($type == 'ideias')
							<a href="{{ route('tasks.others', 'excluidas') }}" class="btn btn-sm fw-bold btn-primary btn-active-danger">
								Visualizar Excluídas
							</a>
							@else
							<a href="{{ route('tasks.others', 'ideias') }}" class="btn btn-sm fw-bold btn-primary btn-active-danger">
								Visualizar Stand-By
							</a>
							@endif
						</div>
					</div>
</div>
@endsection



