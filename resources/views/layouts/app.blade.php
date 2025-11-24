{{--
    Guia de Tema (Fiscalizer + AdminLTE)
    - Variáveis e presets de cores: `resources/css/custom.css` (bloco `:root`).
    - Configuração das classes de tema (AdminLTE): `config/adminlte.php`
        • `classes_sidebar`: adicione `sidebar-custom`
        • `classes_topnav`: adicione `topnav-custom`
        • `classes_content_wrapper`: adicione `content-custom`
        • `classes_body`: adicione `footer-custom theme-fiscalizer`
    - Estas classes ativam o uso das variáveis definidas em `custom.css`.
    - Após mudar cores/variáveis, limpe o cache: `php artisan optimize:clear`.
--}}
@extends('adminlte::page')

{{-- ========================================= --}}
{{-- 🔹 Título do Navegador --}}
{{-- ========================================= --}}
@section('title')
    {{ config('adminlte.title', 'Fiscalizer') }}
    @hasSection('subtitle')
        | @yield('subtitle')
    @endif
@stop

{{-- ========================================= --}}
{{-- 🔹 Cabeçalho da Página --}}
{{-- ========================================= --}}
@section('content_header')

    @hasSection('content_header_title')
        <h1 class="text-muted mb-0">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark ms-2">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif

@stop

@section('content_top_nav_right')
@php
$notiCount = \App\Models\UserNotification::where('user_id', auth()->id())
    ->where('lida', false)->count();

$ultimas = \App\Models\UserNotification::where('user_id', auth()->id())
    ->latest()->limit(5)->get();
@endphp


    <li class="nav-item">
        <a class="nav-link" href="#" id="toggleTheme" aria-label="Alternar tema">
            <i class="fas fa-moon text-white" id="toggleThemeIcon"></i>
        </a>
    </li>
    @include('layouts.components.notificacoes')

    @endsection


{{-- ========================================= --}}
{{-- 🔹 Conteúdo Principal --}}
{{-- ========================================= --}}
@section('content')
<br>
    {{-- Área para inserir o conteúdo da página --}}
    @if(View::hasSection('breadcrumb'))
        @yield('breadcrumb')
    @else
        @include('layouts.components.breadcrumbs')
    @endif
    <div class="app-content-scroll">
        @yield('content_body')
    </div>
    {{-- TOASTS Bootstrap --}}
<div class="position-fixed bottom-0 end-0 p-4 ui-toast-stack">

    {{-- Sucesso --}}
    <div id="toastSuccess" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i> <span id="toastSuccessMsg"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

    {{-- Erro --}}
    <div id="toastError" class="toast align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-times-circle me-2"></i> <span id="toastErrorMsg"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

</div>


    {{-- Mensagem de sucesso (sessão flash) --}}
    @if (session('success'))
        <div class="alert alert-success mt-3">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Stack para scripts adicionais --}}
    @stack('scripts')
@stop

{{-- ========================================= --}}
{{-- 🔹 Rodapé Comum --}}
{{-- ========================================= --}}
@section('footer')
    <div class="float-right text-muted small">
        <b>Versão:</b> {{ config('app.version', '1.0.0') }}
    </div>

    <strong>
        <a href="{{ config('app.company_url', '#') }}" target="_blank">
            {{ config('app.company_name', 'Fiscalizer - Sistema de fiscalização e acompanhamento de contratos') }}
        </a>
    </strong>
@stop

{{-- ========================================= --}}
{{-- 🔹 Scripts Comuns --}}
{{-- ========================================= --}}
@push('js')
    @vite(['resources/js/app.js'])

<script>
    $(function () {
        // JS comum a todas as páginas
        console.log("AdminLTE layout carregado com sucesso!");
    });
</script>
<script>
  window.AppUserId = @json(auth()->id());
  window.AppIsAuthenticated = @json(auth()->check());
  window.CSRFToken = @json(csrf_token());
    </script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
@endpush

{{-- ========================================= --}}
{{-- 🔹 Estilos Comuns --}}
{{-- ========================================= --}}
@push('css')

     @vite(['resources/css/fiscalizer-theme.css'])
     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush


{{-- ========================================= --}}
{{-- 🔹 Card do Usuário (conteúdo extra) --}}
{{-- ========================================= --}}
@section('usermenu_body')
    @php($user = auth()->user())
    @php($profile = \App\Models\UserProfile::where('user_id', $user?->id)->first())
    <div class="px-3">
        <div class="text-muted small">Nome</div>
        <div>{{ $user?->display_name }}</div>


        <div class="text-muted small mt-2">Perfil</div>
        <div>{{ optional($user?->role)->nome ?? '—' }}</div>
    </div>
@endsection

