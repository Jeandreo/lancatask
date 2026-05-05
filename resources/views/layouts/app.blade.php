<!DOCTYPE html>
<html data-bs-theme-mode="system">
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
	<body id="kt_app_body"
			data-kt-app-layout="dark-sidebar"
			data-kt-app-header-fixed="true"
			data-kt-app-sidebar-enabled="true"
			data-kt-app-sidebar-fixed="true"
			data-kt-app-sidebar-hoverable="true"
			data-kt-app-sidebar-push-header="true"
			data-kt-app-sidebar-push-toolbar="true"
			data-kt-app-sidebar-push-footer="true"
			data-kt-app-toolbar-enabled="true"
			class="app-default"
			data-kt-app-sidebar-minimize="@if(Auth::user()->sidebar){{ 'off' }}@else{{ 'on' }}@endif"
			{{-- style="background: url('{{ asset('assets/media/images/background-grow.png') }}');background-position: center center;background-size: 101%;" --}}
			>
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
        <div class="modal fade" id="googleCredentialsMissingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Integração Google Agenda</h3>
                        <button type="button" class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-gray-800 mb-2">
                            Não foi possível conectar com o Google Agenda.
                        </p>
                        <p class="text-gray-700 mb-0">
                            O sistema precisa do arquivo de credenciais do Google (<b>JSON</b>) para concluir a conexão.
                            Peça para o responsável técnico adicionar esse arquivo e tente novamente.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
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
		<script src="{{ asset('assets/js/custom.bundle.js?v=1.0.2') }}"></script>
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
                    url: "{{ route('account.sidebar') }}",
                    data: {_token: @json(csrf_token())},
                });
            });

            // LOAD SOUND
            var enableSound = "{{ Auth::user()->sounds }}" == '0' ? false : true;

            $(document).on('click', '.toggle-sounds', function(){
                $(this).toggleClass('fa-volume-high fa-volume-xmark');
                enableSound = enableSound == true ? false : true;
                $.ajax({
                    type:'PUT',
                    url: "{{ route('account.sounds') }}",
                    data: {_token: @json(csrf_token())},
                });
            });

            // THEME MODE (system | light | dark)
            function resolveThemeMode(mode) {
                if (mode === 'system') {
                    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                return mode;
            }

            function applyThemeMode(mode) {
                var finalMode = resolveThemeMode(mode);
                document.documentElement.setAttribute('data-bs-theme-mode', mode);
                document.documentElement.setAttribute('data-bs-theme', finalMode);
                localStorage.setItem('data-bs-theme', mode);
                $('.theme-mode-option').removeClass('active');
                $('.theme-mode-option[data-theme-mode="' + mode + '"]').addClass('active');
            }

            $(document).on('click', '.theme-mode-option', function(e){
                e.preventDefault();
                applyThemeMode($(this).data('theme-mode'));
            });

            var savedThemeMode = localStorage.getItem('data-bs-theme') || 'system';
            applyThemeMode(savedThemeMode);

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
                if ((localStorage.getItem('data-bs-theme') || 'system') === 'system') {
                    applyThemeMode('system');
                }
            });

            @if(session('google_credentials_missing'))
                var googleModal = new bootstrap.Modal(document.getElementById('googleCredentialsMissingModal'));
                googleModal.show();
            @endif
        </script>
        @yield('custom-footer')
	</body>
</html>
