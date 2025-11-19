@extends('layouts.app')

@section('title', 'Detalhes do Teste de Conectividade')

@section('content_header')
<h1><i class="fas fa-satellite-dish me-2 text-primary"></i> Resultado do Teste</h1>
@stop

@section('content')
@include('layouts.components.breadcrumbs')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h5 class="fw-semibold mb-3">{{ $teste->host->nome_conexao ?? '—' }}</h5>
        <p><strong>IP Testado:</strong> {{ $teste->ip_destino ?? '—' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($teste->status_conexao) }}</p>
        <p><strong>Latência:</strong> {{ $teste->latencia_ms ?? '—' }} ms</p>
        <p><strong>Perda de Pacotes:</strong> {{ $teste->perda_pacotes ?? '—' }}%</p>
        <p><strong>Modo:</strong> {{ ucfirst($teste->modo_execucao) }}</p>
        <p><strong>Executado Por:</strong> {{ $teste->executado_por ?? '—' }}</p>
        <hr>
        <h6 class="fw-semibold">Saída Completa:</h6>
        <pre class="bg-light p-3 rounded-3 text-secondary small">{{ $teste->resultado_json['saida'] ?? '—' }}</pre>
        <a href="{{ route('host_testes.index') }}" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>
</div>
@stop
