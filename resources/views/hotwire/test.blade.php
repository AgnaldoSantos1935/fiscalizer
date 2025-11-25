@extends('layouts.app')
@section('title','Hotwire Teste')
@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Hotwire Turbo – Teste</h5>
      <a href="{{ route('hotwire.test') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </div>
    <div class="card-body">
      <turbo-frame id="demo">
        <div class="d-flex align-items-center gap-3">
          <div class="display-6">0</div>
          <a href="{{ route('hotwire.partial', ['count' => 1]) }}" class="btn btn-primary">Incrementar</a>
        </div>
      </turbo-frame>
    </div>
  </div>
  <div class="mt-3 text-muted small">Navegação e atualização dentro do frame sem recarregar a página.</div>
  <div class="mt-2">
    <a href="{{ route('home') }}" class="btn btn-outline-secondary">Voltar</a>
  </div>
</div>
@endsection
