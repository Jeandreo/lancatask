<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
    <head>
        @include('layouts.head')
    </head>
    <body id="kt_body" class="app-blank" cz-shortcut-listen="true">
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <div class="d-flex flex-column flex-lg-row flex-column-fluid bgi-size-cover bgi-position-center" style="background-image: url({{ asset('assets/media/images/login.png') }})">
                <div class="d-flex flex-column flex-lg-row-fluid">
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                        <div class="w-lg-500px p-10">
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{ route('login') }}" class="form" method="POST">
                                        @csrf
                                        <div class="text-center mb-11">
                                            <img src="{{ asset('assets/media/images/logo.png') }}" class="w-75 mb-4">
                                            <h1 class="text-gray-700 fw-bolder mb-3 fs-2x text-uppercase">
                                                ACESSAR FERRAMENTA
                                            </h1>
                                            <div class="text-gray-500 fw-semibold fs-6">
                                                A ferramenta ideal para escalar a performance dos seus lançamentos!
                                            </div>
                                        </div>
                                        <div class="fv-row mb-3 fv-plugins-icon-container">
                                            <input type="email" placeholder="E-mail" name="email" autocomplete="off" class="form-control form-control-solid" value="{{ old('email') }}" required>
                                        </div>
                                        <div class="fv-row mb-4 fv-plugins-icon-container">
                                            <div class="position-relative mb-3">
                                                <input class="form-control form-control-lg form-control-solid" type="password" name="password" placeholder="Senha" required />
                                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 eye-password"
                                                    data-kt-password-meter-control="visibility">
                                                        <i class="ki-duotone ki-eye-slash fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                                        <i class="ki-duotone ki-eye d-none fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="fv-row mb-4 fv-plugins-icon-container">
                                            <label class="form-check form-check-inline">
                                                <input class="form-check-input cursor-pointer" type="checkbox" name="remember" value="1">
                                                <span class="form-check-label fw-semibold text-gray-700 fs-base ms-1 cursor-pointer">
                                                    Mantenha-me conectado!
                                                </span>
                                            </label>
                                        </div>
                                        <div class="d-grid mb-10">
                                            <button type="submit" class="btn btn-info">
                                                Acessar
                                            </button>
                                        </div>
                                    </form>
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var hostUrl = "assets/";
        </script>
        <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/custom/authentication/sign-in/general.js') }}"></script>
        <script>
           $('.eye-password').click(function(){
                let passwordField = $('[name="password"]');
                let fieldType = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', fieldType);
            });

        </script>
    </body>
</html>
