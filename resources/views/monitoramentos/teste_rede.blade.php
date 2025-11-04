@extends('layouts.app')
@section('title', 'Teste de IPs e Domínios')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-0">
      <h4 class="text-secondary fw-semibold">
        <i class="fas fa-network-wired text-primary me-2"></i> Teste de IPs e Domínios
      </h4>
    </div>
    <div class="card-body">
      <form action="{{ route('monitoramentos.testar') }}" method="POST" class="row g-3 mb-4">
        @csrf
        <div class="col-md-9">
          <label class="form-label fw-semibold small text-secondary">Endereço IP ou Domínio</label>
          <input type="text" name="alvo" class="form-control form-control-lg" placeholder="Ex: 8.8.8.8 ou www.google.com" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button type="submit" class="btn btn-success w-100">
            <i class="fas fa-play me-1"></i> Testar
          </button>
        </div>
      </form>

      @isset($dados)
      <div class="row">
        <div class="col-md-4 mb-3">
          <div class="card border-start border-4 border-info">
            <div class="card-body">
              <h6 class="text-muted">Tipo</h6>
              <h5 class="fw-bold">{{ $dados['tipo'] }}</h5>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card border-start border-4 {{ str_contains($dados['ping'], 'Alcançável') ? 'border-success' : 'border-danger' }}">
            <div class="card-body">
              <h6 class="text-muted">Ping</h6>
              <h5 class="fw-bold">{{ $dados['ping'] }}</h5>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card border-start border-4
              @if($dados['http_ok']) border-success
              @elseif($dados['http_status']) border-warning
              @else border-danger @endif">
            <div class="card-body">
              <h6 class="text-muted">HTTP Status</h6>
              <h5 class="fw-bold">{{ $dados['http_status'] ?? '—' }}</h5>
              <small class="text-muted">{{ $dados['tempo_resposta'] ?? '' }}</small>
            </div>
          </div>
        </div>
      </div>

      <div class="alert alert-light border mt-4">
        <strong>Alvo:</strong> {{ $dados['alvo'] }}<br>
        <strong>DNS:</strong> {{ $dados['dns'] ?? 'Não resolvido' }}<br>
        <strong>Resultado HTTP:</strong>
        {{ $dados['http_ok'] ? 'Disponível ✅' : 'Indisponível ❌' }}
      </div>
      @endisset
    </div>
  </div>
</div>
@endsection
