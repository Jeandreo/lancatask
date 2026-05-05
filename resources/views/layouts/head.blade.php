<title>@yield('Page Title', 'LançaTask - Gerencie e organize seus lançamentos com eficiência!') - LançaTask</title>
<meta charset="utf-8" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="description" content="LançaTask é o sistema ideal para gerenciar e organizar tarefas relacionadas a lançamentos. Simplifique processos, otimize tempo e acompanhe tudo em um só lugar. Experimente agora!" />
<meta name="keywords" content="gerenciamento de tarefas, lançamentos, organização, produtividade, tarefas, gestão de projetos, software de tarefas, controle de lançamentos, LançaTask, planejamento, time de lançamento" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta property="og:locale" content="pt_BR" />
<meta property="og:type" content="website" />
<meta property="og:title" content="LançaTask - Gerencie e organize seus lançamentos com eficiência!" />
<meta property="og:url" content="{{ config('app.url') }}" />
<meta property="og:site_name" content="LançaTask" />
<meta property="og:description" content="Simplifique o gerenciamento de tarefas para lançamentos com o LançaTask. Acompanhe o progresso do seu time e garanta sucesso nos seus projetos." />
<link rel="canonical" href="{{ config('app.url') }}" />
<link rel="shortcut icon" href="{{ asset('assets/media/images/favicon-lanca-task-small.png') }}" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom.bundle.css?v=1.0.1') }}" rel="stylesheet" type="text/css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
<script src="{{ asset('assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
<style>


[data-bs-theme="dark"]:root {
    --bs-menu-link-bg-color-active: #1c0428 !important;
    --bs-menu-link-bg-color-hover: #1c0428 !important;
    --bs-primary-light: #15091c;
    --bs-light: #281036;
}

</style>
@yield('custom-head')
