<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		@include('layouts.head')
        @if (isset($pageClean))
            <style>
                @media (min-width: 992px) {
                    [data-kt-app-header-fixed=true] .app-wrapper {
                        margin-top: 0;
                    }
                }
            </style>
        @endif
	</head>
	<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default"  data-kt-app-sidebar-minimize="@if(Auth::user()->sidebar){{ 'off' }}@else{{ 'on' }}@endif">
        @include('layouts.config')
		<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
			<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
                @if(!isset($pageClean))
                @include('layouts.header')
                @endif
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
					@include('layouts.sidebar')
					<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
						<div class="d-flex flex-column flex-column-fluid">
							<div id="kt_app_content" class="app-content flex-column-fluid">
								<div id="kt_app_content_container" class="@if(!isset($pageClean)) app-container container-fluid py-6 @else p-6 @endif">
                                    @yield('content')
								</div>
							</div>
						</div>
						{{-- @include('layouts.footer') --}}
					</div>
				</div>
			</div>
		</div>
        @include('includes.preview')
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<i class="ki-duotone ki-arrow-up">
				<span class="path1"></span>
				<span class="path2"></span>
			</i>
		</div>
        <script>
            var hostUrl = "assets/";
			var globalUrl = "{{ route('dashboard.index') }}";
			var csrf = "{{ csrf_token() }}";
        </script>
        {{-- <script src="{{ asset('assets/js/sidebar.bundle.js') }}"></script> --}}
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
		<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/pt.min.js"></script>
		<script src="{{ asset('assets/js/custom.bundle.js') }}"></script>
        <script src="{{ asset('assets/plugins/custom/ckeditor5/ckeditor-classic.bundle.js') }}"></script>
        <script>
			// CONFIRGURAÇÕES NOTIFICAÇÕES
			toastr.options = {
				"closeButton": false,
				"debug": false,
				"newestOnTop": false,
				"progressBar": true,
				"positionClass": "toast-top-right",
				"preventDuplicates": false,
				"onclick": null,
				"showDuration": "300",
				"hideDuration": "1500",
				"timeOut": "8000",
				"extendedTimeOut": "2000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			};

			// VERIFICA SE TEM ALERTA
			var message = '{!! session("message") !!}';

			// SE EXISTIR EXIBIR
			if(message != ""){

				// PEGA O TIPO DA NOTIFICAÇÃO
				var type = "{!! session('type') !!}";
				var title = "{!! session('title') !!}";

				// EXIBE NOTIFICAÇÃO
				if(type == 'success'){
					toastr.success(message, title);
				} else if(type == 'error') {
					toastr.error(message, title);
				} else if(type == 'warning') {
					toastr.warning(message, title);
				} else {
					toastr.info(message, title);
				}

			}
            // SAVE STATE SIDEBAR
            $(document).on('click', '.toggle-sidebar', function(){
                $.ajax({
                    type:'PUT',
                    url: "{{ route('users.sidebar') }}",
                    data: {_token: @json(csrf_token())},
                });
            });
        </script>
        @yield('custom-footer')
	</body>
</html>
