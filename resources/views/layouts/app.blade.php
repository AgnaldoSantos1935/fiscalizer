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
            {{ config('app.company_name', 'Secretaria de Estado de Educação do Pará') }}
        </a>
    </strong>
@stop

{{-- ========================================= --}}
{{-- 🔹 Scripts Comuns --}}
{{-- ========================================= --}}
@push('js')
<script>
    $(document).ready(function () {
        // JS comum a todas as páginas
        console.log("AdminLTE layout carregado com sucesso!");
    });
</script>
@endpush

{{-- ========================================= --}}
{{-- 🔹 Estilos Comuns --}}
{{-- ========================================= --}}
@push('css')
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
