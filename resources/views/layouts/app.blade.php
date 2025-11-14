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

{{-- ========================================= --}}
{{-- 🔹 Conteúdo Principal --}}
{{-- ========================================= --}}
@section('content')
    {{-- Área para inserir o conteúdo da página --}}
    @yield('content_body')
    {{-- TOASTS Bootstrap --}}
<div class="position-fixed bottom-0 end-0 p-4" style="z-index: 9999">

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
@endpush

{{-- ========================================= --}}
{{-- 🔹 Estilos Comuns --}}
{{-- ========================================= --}}
@push('css')
    @vite(['resources/css/app.css'])
<style>
    .card-header {
        border-bottom: none !important;
    }

    .card-title {
        font-weight: 600;
    }

    .content-wrapper {
        background-color: #f8f9fa !important;
    }

    .alert-success {
        border-left: 5px solid #198754;
    }

</style>
@endpush

