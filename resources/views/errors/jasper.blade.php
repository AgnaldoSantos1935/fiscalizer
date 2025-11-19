@extends('layouts.app')

@section('title', 'Erro ao gerar relatório')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0 rounded-4 ui-card">
    <div class="card-header bg-ui-primary">
      <h4 class="card-title fw-semibold">JasperReports</h4>
    </div>
    <div class="card-body">
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ $message ?? 'Falha desconhecida ao gerar relatório.' }}
      </div>
      <p class="text-muted mb-2">Verifique se o JasperStarter está instalado e acessível no PATH.</p>
      <ul>
        <li>Windows: instale JasperStarter e garanta <code>jasperstarter.exe</code> no PATH.</li>
        <li>Java: instale JRE/JDK 8+.</li>
        <li>Drivers: MySQL drivers disponíveis (JasperStarter normalmente inclui).</li>
      </ul>
    </div>
  </div>
  <div class="mt-3">
    <a href="{{ route('relatorios.jasper.demo') }}" class="btn btn-primary">Tentar novamente</a>
  </div>
</div>
@endsection