@extends('layouts.app')

@section('title', 'Dashboard - Fiscalizer')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

  <!-- 🔹 Apresentação e Avisos -->
  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="card ui-card shadow-sm border-0 rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-home text-primary fa-lg me-2"></i>
            <h5 class="mb-0">Bem-vindo{{ isset($usuario) && $usuario ? ", ".$usuario->name : '' }}!</h5>
          </div>
          <p class="text-muted mb-3">Esta é a sua página inicial. Aqui você encontra atalhos para as funcionalidades principais e um resumo de avisos para sua conta.</p>

          <div class="row g-3">
            <div class="col-sm-6 col-md-4">
              <a href="{{ route('contratos.index') }}" class="text-decoration-none">
                <div class="ui-card p-3 h-100 hover-shadow">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-file-contract text-primary me-2"></i><strong>Contratos</strong></div>
                  <small class="text-muted">Cadastro, gestão e conformidade de contratos.</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6 col-md-4">
              <a href="{{ route('projetos.index') }}" class="text-decoration-none">
                <div class="ui-card p-3 h-100 hover-shadow">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-diagram-project text-success me-2"></i><strong>Projetos</strong></div>
                  <small class="text-muted">Portfólio, produtividade e indicadores.</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6 col-md-4">
              <a href="{{ route('medicoes.index') }}" class="text-decoration-none">
                <div class="ui-card p-3 h-100 hover-shadow">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-calculator text-info me-2"></i><strong>Medições</strong></div>
                  <small class="text-muted">Ciclos de medição e boletins.</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6 col-md-4">
              <a href="{{ route('mapas.escolas') }}" class="text-decoration-none">
                <div class="ui-card p-3 h-100 hover-shadow">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-map-marked-alt text-warning me-2"></i><strong>Mapas</strong></div>
                  <small class="text-muted">Exploração geográfica das escolas e filtros.</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6 col-md-4">
              <a href="{{ route('monitoramentos.index') }}" class="text-decoration-none">
                <div class="ui-card p-3 h-100 hover-shadow">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-server text-secondary me-2"></i><strong>Monitoramentos</strong></div>
                  <small class="text-muted">Saúde dos serviços e hosts monitorados.</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6 col-md-4">
              <a href="{{ route('notificacoes.index') }}" class="text-decoration-none">
                <div class="ui-card p-3 h-100 hover-shadow">
                  <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-bell text-danger me-2"></i><strong>Notificações</strong>
                    @if(($notificacoesNaoLidas ?? 0) > 0)
                      <span class="badge bg-danger ms-2">{{ $notificacoesNaoLidas }}</span>
                    @endif
                  </div>
                  <small class="text-muted">Avisos e alertas da sua conta.</small>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card ui-card shadow-sm border-0 rounded-4 h-100">
        <div class="card-header ui-card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0 text-secondary fw-semibold"><i class="fas fa-bell text-danger me-1"></i>Avisos</h6>
          <a href="{{ route('notificacoes.index') }}" class="btn btn-sm ui-btn outline">Ver todas</a>
        </div>
        <div class="card-body">
          @forelse(($ultimasNotificacoes ?? collect()) as $n)
            <div class="mb-3">
              <div class="d-flex align-items-center">
                @if(!$n->lida)
                  <span class="badge bg-danger me-2">Nova</span>
                @else
                  <span class="badge bg-secondary me-2">Lida</span>
                @endif
                <strong>{{ $n->titulo }}</strong>
              </div>
              <div class="small text-muted">{{ $n->mensagem }}</div>
              <div class="d-flex justify-content-between mt-1">
                <div class="small text-muted">{{ optional($n->created_at)->format('d/m/Y H:i') }}</div>
                @if($n->link)
                  <a href="{{ $n->link }}" class="small">Abrir</a>
                @endif
              </div>
            </div>
          @empty
            <div class="text-muted">Sem avisos recentes.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-12">
      <h3 class="fw-semibold text-secondary">
        <i class="fas fa-tachometer-alt text-primary me-2"></i>Painel Geral do Sistema Fiscalizer
      </h3>
      <p class="text-muted mb-0">Visão consolidada de contratos, projetos e medições</p>
    </div>
  </div>

  <!-- 🔹 Indicadores principais -->
  <div class="row g-3 mb-4">
    <div class="col-md-2">
      <div class="card bg-primary text-white text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-file-contract fa-2x mb-2"></i>
          <h4>{{ $totalContratos }}</h4>
          <small>Contratos</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card bg-success text-white text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-diagram-project fa-2x mb-2"></i>
          <h4>{{ $totalProjetos }}</h4>
          <small>Projetos</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card bg-info text-white text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-calculator fa-2x mb-2"></i>
          <h4>{{ $totalMedicoes }}</h4>
          <small>Medições</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card bg-warning text-dark text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
          <h4>{{ $totalBoletins }}</h4>
          <small>Boletins</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card bg-secondary text-white text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-cogs fa-2x mb-2"></i>
          <h4>{{ number_format($totalPF, 0, ',', '.') }}</h4>
          <small>Total PF</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card bg-danger text-white text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-dollar-sign fa-2x mb-2"></i>
          <h4>R$ {{ number_format($valorTotal, 2, ',', '.') }}</h4>
          <small>Valor Executado</small>
        </div>
      </div>
    </div>
  </div>

  <!-- 🔹 Gráfico Top Projetos -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-light">
      <h6 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-chart-bar text-primary me-1"></i>Top 5 Projetos (Pontos de Função e UST)
      </h6>
    </div>
    <div class="card-body bg-white">
      <canvas id="graficoTopProjetos"></canvas>
    </div>
  </div>

  <!-- 🔹 Últimos boletins emitidos -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-light">
      <h6 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-clock text-success me-1"></i>Boletins Recentes
      </h6>
    </div>
    <div class="card-body bg-white p-0">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Nº</th>
            <th>Projeto</th>
            <th>Contrato</th>
            <th>Medição</th>
            <th class="text-end">Valor (R$)</th>
            <th class="text-center">Data</th>
          </tr>
        </thead>
        <tbody>
          @forelse($boletinsRecentes as $b)
            <tr>
              <td>{{ $b->id }}</td>
              <td>{{ $b->projeto->nome ?? '—' }}</td>
              <td>{{ $b->medicao->contrato->numero ?? '—' }}</td>
              <td>{{ $b->medicao->mes_referencia ?? '—' }}</td>
              <td class="text-end">R$ {{ number_format($b->valor_total, 2, ',', '.') }}</td>
              <td class="text-center">{{ $b->data_emissao->format('d/m/Y') }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-3">Nenhum boletim recente encontrado.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctxTopProjetos = document.getElementById('graficoTopProjetos');
    new Chart(ctxTopProjetos, {
        type: 'bar',
        data: {
            labels: @json($topProjetos->pluck('projeto')),
            datasets: [
                {
                    label: 'Pontos de Função (PF)',
                    data: @json($topProjetos->pluck('total_pf')),
                    backgroundColor: '#007bff'
                },
                {
                    label: 'UST',
                    data: @json($topProjetos->pluck('total_ust')),
                    backgroundColor: '#28a745'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
@endsection
