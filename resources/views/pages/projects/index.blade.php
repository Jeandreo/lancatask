@extends('layouts.app')

@section('title-page', 'Categorias')

@section('title-toolbar', 'Categorias')

@section('content')
	@include('layouts.title')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<ul class="nav nav-tabs nav-line-tabs justify-content-center fs-3">
						<li class="nav-item">
							<a class="nav-link text-uppercase fw-bold text-gray-700 active" data-bs-toggle="tab" href="#tab_expense">Despesa</a>
						</li>
						<li class="nav-item">
							<a class="nav-link text-uppercase fw-bold text-gray-700" data-bs-toggle="tab" href="#tab_revenue">Receita</a>
						</li>
					</ul>
					<div class="tab-content">
						@foreach (['expense', 'revenue'] as $tab)
						<div class="tab-pane fade show @if($tab == 'expense') active @endif" id="tab_{{ $tab }}" role="tabpanel">
							<table class="table table-striped table-row-bordered gy-3 gs-7 border rounded align-middle dmk-datatables">
								<thead>
									<tr class="fw-bold fs-6 text-gray-800 px-7">
										<th>Nome</th>
										<th class="text-center" width="110px">Status</th>
										<th class="text-center" width="165px">
											<span>Ações</span>
										</th>
									</tr>
								</thead>
								<tbody>
								@if (isset($contents[$tab]))
									@foreach ($contents[$tab] as $content)
									<tr>
										<td>
											<a href="{{ route('financial.categories.edit', $content->id) }}" class="d-flex align-items-center fs-6 fw-normal">
												<div class="w-25px h-25px rounded-circle d-flex justify-content-center align-items-center me-2" style="background: {{ $content->color }};">
													<i class="{{ $content->icon }} fs-7 text-white"></i>
												</div>
												<span class="text-gray-800 text-hover-primary">
													{{ $content->name }}
												</span>
											</a>
										</td>
										<td class="text-center">
											@if($content->status == 1) 
											<span class="badge badge-light-success">
												Ativo
											</span>
											@else
											<span class="badge badge-light-danger">
												Inativo
											</span>
											@endif
										</td>
										<td class="text-center">
											<a href="{{ route('financial.categories.edit', $content->id) }}" class="btn btn-sm btn-light btn-active-light-success btn-icon">
												<i class="fa-solid fa-pen-to-square "></i>
											</a>
											<a href="{{ route('financial.categories.destroy', $content->id) }}" class="btn btn-sm btn-light btn-active-light-danger btn-icon">
												<i class="fa-solid fa-trash-can"></i>
											</a>
										</td>
									</tr>
										@foreach ($content->childrens as $children)
										<tr>
											<td class="py-0">
												<a href="{{ route('financial.categories.edit', $children->id) }}" class="d-flex align-items-center text-gray-800 text-hover-primary fs-6 fw-normal ps-6">
													<div class="w-10px h-10px rounded-circle d-flex justify-content-center align-items-center me-2" style="background: {{ $children->father->color }};"></div>
													{{ $children->name }}
												</a>
											</td>
											<td class="py-0"></td>
											<td class="text-center py-1">
												<a href="{{ route('financial.categories.edit', $children->id) }}" class="btn h-30px w-30px fs-6 btn-light btn-active-light-success btn-icon">
													<i class="fa-solid fa-pen-to-square"></i>
												</a>
												<a href="{{ route('financial.categories.destroy', $children->id) }}" class="btn h-30px w-30px fs-6 btn-light btn-active-light-success btn-icon">
													<i class="fa-solid fa-trash-can"></i>
												</a>
											</td>
										</tr>
										@endforeach
									@endforeach
								@endif
								</tbody>
							</table>
						</div>
						@endforeach
					</div>
				</div>
			</div>
			<div class="d-flex justify-content-between mt-6">
				<a href="{{ route('dashboard.index') }}" class="btn btn-sm fw-bold btn-secondary">Voltar</a>
				<a href="{{ route('financial.categories.create') }}" class="btn btn-sm fw-bold btn-primary btn-active-danger">Adicionar Categoria</a>
			</div>
		</div>
	</div>
@endsection