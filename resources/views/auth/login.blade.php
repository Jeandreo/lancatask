<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
    <!--begin::Head-->
    <head>
        @include('layouts.head')
    </head>
    <!--end::Head-->
    <!--begin::Body-->
    <body id="kt_body" class="app-blank" cz-shortcut-listen="true">
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Body-->
                <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1 bg-white">
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                        <div class="w-lg-500px p-10">
                            <form action="{{ route('login') }}" class="form" method="POST">
                                @csrf
                                <div class="text-center mb-11">
                                    <h1 class="text-gray-700 fw-bolder mb-3 fs-2x text-uppercase">
                                        Acessar Lança Task
                                    </h1>
                                    <div class="text-gray-500 fw-semibold fs-6">
                                        A ferramenta ideal para disparar sua performance nos lançamentos digitais!
                                    </div>
                                </div>
                                <div class="separator separator-content my-14">
                                    <span class="w-125px text-gray-500 fw-semibold fs-7">Ou com email</span>
                                </div>
                                <div class="fv-row mb-8 fv-plugins-icon-container">
                                    <input type="email" placeholder="E-mail" name="email" autocomplete="off" class="form-control form-control-solid" value="{{ old('email') }}" required>
                                </div>
                                <div class="fv-row mb-3 fv-plugins-icon-container">
                                    <div class="position-relative mb-3">
                                        <input class="form-control form-control-lg form-control-solid" type="password" name="password" placeholder="Senha" required />
                                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 eye-password"
                                            data-kt-password-meter-control="visibility">
                                                <i class="ki-duotone ki-eye-slash fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                                <i class="ki-duotone ki-eye d-none fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-grid mb-10">
                                    <button type="submit" class="btn btn-primary">
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
                <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" style="background-image: url({{ asset('assets/media/images/background.jpg') }})">
                    <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                        <img alt="Logo" src="{{ asset('assets/media/images/logo-white.png') }}" class="h-35px h-lg-70px mb-2">
                        <h1 class="text-white fs-2qx fw-bolder text-center mb-7">
                            Rápido, Fácil e Eficiente
                        </h1>
                        <div class="text-white fs-base text-center">
                            Esta ferramenta foi desenvolvida por uma pessoa que estava em busca de sua <span class="text-warning fw-bold">melhor versão</span>, utilize-a da maneira que desejar,<br> o objetivo dela é lhe ajudar a chegar mais longe, organizando seus objetivos, metas e avalizando sua performance.
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
