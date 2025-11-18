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

@section('topnav_right')
@php
$notiCount = \App\Models\UserNotification::where('user_id', auth()->id())
    ->where('lida', false)->count();

$ultimas = \App\Models\UserNotification::where('user_id', auth()->id())
    ->latest()->limit(5)->get();
@endphp


    @include('layouts.components.notificacoes')

    @endsection


{{-- ========================================= --}}
{{-- 🔹 Conteúdo Principal --}}
{{-- ========================================= --}}
@section('content')
    {{-- Breadcrumb opcional fornecido pela view --}}
    @hasSection('breadcrumb')
        <div class="container-fluid mb-2">
            @yield('breadcrumb')
        </div>
    @endif
    @if (!View::hasSection('breadcrumb'))
        @php $segs = request()->segments(); $acc=''; @endphp
        <div class="container-fluid mb-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-primary fw-semibold">Início</a></li>
                    @foreach($segs as $i => $seg)
                        @php $acc .= '/'.$seg; @endphp
                        @if($i < count($segs)-1)
                            <li class="breadcrumb-item"><a href="{{ url($acc) }}" class="text-decoration-none text-primary fw-semibold">{{ ucfirst(str_replace('-', ' ', $seg)) }}</a></li>
                        @else
                            <li class="breadcrumb-item active text-secondary fw-semibold">{{ ucfirst(str_replace('-', ' ', $seg)) }}</li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
    @endif
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
<script>
  window.AppUserId = @json(auth()->id());
  window.AppIsAuthenticated = @json(auth()->check());
  window.CSRFToken = @json(csrf_token());
</script>
<script>
(function(){
  function formatInput(inp){
    var raw = inp.value || '';
    var digits = raw.replace(/\D/g,'');
    var num = (parseInt(digits || '0',10))/100;
    inp.value = num.toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
  }
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('input.money-br-input').forEach(function(inp){
      inp.addEventListener('input', function(){ formatInput(inp); });
      inp.addEventListener('focus', function(){ if(!inp.value){ inp.value = 'R$ 0,00'; } });
      if(inp.value){ formatInput(inp); }
    });
    document.querySelectorAll('[data-format="currency"]').forEach(function(el){
      var val = el.getAttribute('data-value') || el.textContent;
      var num = parseFloat(String(val).replace(/[^\d,.-]/g,'').replace(/\./g,'').replace(',','.'));
      if(!isNaN(num)){
        el.textContent = num.toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
      }
    });
  });
})();
</script>
<script>
(function(){
  function formatCNPJ(inp){
    var d = (inp.value || '').replace(/\D/g,'').slice(0,14);
    var out = d;
    if(d.length > 2 && d.length <= 5){ out = d.slice(0,2)+'.'+d.slice(2); }
    else if(d.length > 5 && d.length <= 8){ out = d.slice(0,2)+'.'+d.slice(2,5)+'.'+d.slice(5); }
    else if(d.length > 8 && d.length <= 12){ out = d.slice(0,2)+'.'+d.slice(2,5)+'.'+d.slice(5,8)+'/'+d.slice(8); }
    else if(d.length > 12){ out = d.slice(0,2)+'.'+d.slice(2,5)+'.'+d.slice(5,8)+'/'+d.slice(8,12)+'-'+d.slice(12,14); }
    inp.value = out;
  }
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('input.cnpj-input').forEach(function(inp){
      inp.addEventListener('input', function(){ formatCNPJ(inp); });
      if(inp.value){ formatCNPJ(inp); }
    });
  });
})();
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
    .notif-pulse > a .fa-bell {
    position: relative;
}

.notif-pulse > a .fa-bell::after {
    content: '';
    position: absolute;
    top: -4px;
    right: -4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #ff4757;
    box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7);
    animation: pulse-notif 1.5s infinite;
}

@keyframes pulse-notif {
    0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7); }
    70% { transform: scale(1.3); box-shadow: 0 0 0 10px rgba(255, 71, 87, 0); }
    100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(255, 71, 87, 0); }
}

/* Ordenação: sino antes da área do usuário no topo */
.navbar-nav .notif-dropdown { order: 0; }
.navbar-nav .user-menu { order: 1; }
</style>
@endpush
