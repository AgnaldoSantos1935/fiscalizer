@extends('layouts.app')
@section('title', 'Visualizar Documento')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Visualizar Documento</h2>
    <div>
      <a href="{{ route('documentos.download', $documento) }}" class="btn btn-outline-success me-2">
        <i class="fas fa-download"></i> Download
      </a>
      <a href="{{ route('documentos.stream', $documento) }}" target="_blank" class="btn btn-outline-primary">
        Abrir em nova aba
      </a>
      <a href="{{ $return_to }}" class="btn btn-secondary">Voltar</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="ratio ratio-16x9">
        <iframe
          id="pdfFrame"
          src="{{ route('documentos.stream', $documento) }}"
          title="Visualizador de PDF"
          class="border-0 w-100 h-100"
          allow="fullscreen"
        ></iframe>
      </div>
    </div>
    <div class="card-footer text-muted small">
      <div><strong>Título:</strong> {{ $documento->titulo ?? basename($documento->caminho_arquivo) }}</div>
      <div><strong>Tipo:</strong> {{ $documento->tipo }}</div>
      <div><strong>Arquivo:</strong> {{ $documento->caminho_arquivo }}</div>
      @if($documento->contrato)
        <div><strong>Contrato:</strong> #{{ $documento->contrato->id }} — {{ $documento->contrato->numero }}</div>
      @endif
    </div>
  </div>
</div>

{{-- Botão de imprimir removido conforme solicitado --}}
@endsection