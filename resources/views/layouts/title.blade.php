<div class="page-title d-flex flex-column justify-content-center flex-wrap mb-5">
	<h1 class="page-heading text-gray-700 fw-bold fs-3 flex-column justify-content-center my-0 text-center">
	   @yield('Page Title', '#######')
	</h1>
	<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1 justify-content-center">
	   	<li class="breadcrumb-item text-muted">
		  	<a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">
				Home
			</a>
		</li>
		<li class="breadcrumb-item">
			<span class="bullet bg-gray-500 w-5px h-2px"></span>
		</li>
		<li class="breadcrumb-item text-muted">
			@yield('Page Title', '#######')                                            
		</li>
	</ul>
</div>