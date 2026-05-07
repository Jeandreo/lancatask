<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
    <head>
        @include('layouts.head')
    </head>
    <body id="kt_body" class="app-blank" cz-shortcut-listen="true">
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <div class="d-flex flex-column flex-lg-row flex-column-fluid bgi-size-cover bgi-position-center" style="background: #1c1c1c;">
                <div class="d-flex flex-column flex-lg-row-fluid">
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid bg-dark">
                        <div class="w-lg-550px">
                            <div class="card">
                                <div class="card-body p-14">
                                    <form action="{{ route('quick.access.store') }}" class="form" method="POST">
                                        @csrf
                                        <div class="text-center mb-11">
                                            <img src="{{ asset('assets/media/images/logo-lanca-task-branca.png') }}" class="w-75 mb-4">
                                            <h1 class="text-gray-900 fw-bolder mb-3 fs-2x text-uppercase">Acesso rápido</h1>
                                            <div class="text-gray-500 fw-semibold fs-6">
                                                Informe o token de acesso para entrar como usuário 1.
                                            </div>
                                        </div>

                                        <div class="fv-row mb-4 fv-plugins-icon-container">
                                            <div class="position-relative mb-3">
                                                <input class="form-control form-control-lg form-control-solid" type="password" name="token" placeholder="Token de acesso" value="{{ old('token') }}" required>
                                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 eye-password">
                                                    <i class="ki-duotone ki-eye-slash fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                                    <i class="ki-duotone ki-eye d-none fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="d-grid mb-3">
                                            <button type="submit" class="btn btn-info">Entrar como usuário 1</button>
                                        </div>

                                        <div class="text-center">
                                            <a href="{{ route('login') }}" class="text-gray-500">Voltar ao login padrão</a>
                                        </div>
                                    </form>

                                    @if ($errors->any())
                                        <div class="alert alert-danger mt-4">
                                            <ul class="mb-0">
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

        <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
        <script>
            $('.eye-password').click(function () {
                let field = $('[name="token"]');
                let fieldType = field.attr('type') === 'password' ? 'text' : 'password';
                field.attr('type', fieldType);
            });
        </script>
    </body>
</html>
