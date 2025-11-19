@extends('layouts.app')
@section('title', 'Teste de IPs e Domínios')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h4 class="text-secondary fw-semibold mb-0">
        <i class="fas fa-network-wired text-primary me-2"></i>
        Teste de IPs e Domínios
      </h4>
    </div>

    <div class="card-body">
      {{-- 🔹 Formulário de teste manual --}}
      <form action="{{ route('teste_conexao.testar') }}" method="POST" class="row g-3 mb-4">
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

      {{-- 🔹 Resultado do teste manual --}}
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

  {{-- 🔹 Hosts cadastrados para monitoramento automático --}}
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h4 class="text-secondary fw-semibold mb-0">
        <i class="fas fa-server text-primary me-2"></i>
        Hosts Monitorados Automaticamente
      </h4>
      <a href="{{ route('monitoramentos.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-list"></i> Gerenciar Monitoramentos
      </a>
    </div>

    <div class="card-body">
      @if($teste->isEmpty())
        <div class="alert alert-light border text-center text-muted">
          Nenhum host cadastrado para monitoramento automático.
        </div>
      @else
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Nome</th>
              <th>Tipo</th>
              <th>Alvo</th>
              <th>Status</th>
              <th>Latência</th>
              <th>Última Verificação</th>
              <th class="text-center">Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($teste as $m)
            <tr>
              <td>{{ $m->nome }}</td>
              <td>{{ strtoupper($m->tipo) }}</td>
              <td>{{ $m->alvo }}</td>
              <td>
                {!! $m->online
                    ? '<span class="badge bg-success">Online</span>'
                    : '<span class="badge bg-danger">Offline</span>' !!}
              </td>
              <td>{{ $m->latencia ? number_format($m->latencia, 2).' ms' : '—' }}</td>
              <td>{{ $m->ultima_verificacao ? $m->ultima_verificacao->format('d/m/Y H:i') : '—' }}</td>
              <td class="text-center">
                <a href="{{ route('monitoramentos.historico', $m->id) }}" class="btn btn-outline-primary btn-sm" title="Histórico">
                  <i class="fas fa-chart-line"></i>
                </a>
                <a href="{{ route('monitoramentos.testar', $m->id) }}" class="btn btn-outline-success btn-sm" title="Testar Agora">
                  <i class="fas fa-sync"></i>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
