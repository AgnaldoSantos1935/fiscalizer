@extends('layouts.app')

@section('title', 'Painel de Projetos e Medições')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

  <div class="row mb-4">
    <div class="col-md-12">
      <h3 class="fw-semibold text-secondary">
        <i class="fas fa-tachometer-alt text-primary me-2"></i>Painel de Acompanhamento de Projetos e Medições
      </h3>
      <p class="text-muted mb-0">Monitoramento geral de desempenho técnico e financeiro dos projetos de TI (Fiscalizer)</p>
    </div>
  </div>

  <!-- 🔹 Indicadores principais -->
  <div class="row g-3 mb-4">
    <div class="col-md-2">
      <div class="card bg-primary text-white text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-folder-open fa-2x mb-2"></i>
          <h4>{{ $totalProjetos }}</h4>
          <small>Projetos Ativos</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card bg-success text-white text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
          <h4>{{ $totalBoletins }}</h4>
          <small>Boletins Emitidos</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card bg-warning text-dark text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-calculator fa-2x mb-2"></i>
          <h4>{{ number_format($totalPF, 0, ',', '.') }}</h4>
          <small>Pontos de Função</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card bg-info text-white text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-microchip fa-2x mb-2"></i>
          <h4>{{ number_format($totalUST, 0, ',', '.') }}</h4>
          <small>Total de UST</small>
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

    <div class="col-md-2">
      <div class="card bg-secondary text-white text-center shadow-sm border-0 rounded-4">
        <div class="card-body">
          <i class="fas fa-chart-line fa-2x mb-2"></i>
          <h4>{{ $totalMedicoes }}</h4>
          <small>Medições Registradas</small>
        </div>
      </div>
    </div>
  </div>

  <!-- 🔹 Gráfico PF/UST por Projeto -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-light">
      <h6 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-chart-bar text-primary me-1"></i>Produtividade por Projeto
      </h6>
    </div>
    <div class="card-body bg-white">
      <canvas id="graficoPfUst"></canvas>
    </div>
  </div>

  <!-- 🔹 Status e Equipes -->
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light">
          <h6 class="mb-0 text-secondary fw-semibold"><i class="fas fa-tasks text-info me-1"></i>Status de Execução</h6>
        </div>
        <div class="card-body bg-white">
          <canvas id="graficoStatus"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light">
          <h6 class="mb-0 text-secondary fw-semibold"><i class="fas fa-users text-success me-1"></i>Equipe com Maior Participação</h6>
        </div>
        <div class="card-body bg-white">
          <canvas id="graficoEquipe"></canvas>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctxPfUst = document.getElementById('graficoPfUst');
    const ctxStatus = document.getElementById('graficoStatus');
    const ctxEquipe = document.getElementById('graficoEquipe');

    // 🔹 PF/UST por Projeto
    new Chart(ctxPfUst, {
        type: 'bar',
        data: {
            labels: @json($pfPorProjeto->pluck('projeto')),
            datasets: [
                {
                    label: 'Pontos de Função (PF)',
                    data: @json($pfPorProjeto->pluck('total_pf')),
                    backgroundColor: '#007bff'
                },
                {
                    label: 'UST',
                    data: @json($pfPorProjeto->pluck('total_ust')),
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

    // 🔹 Status de Execução
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: @json($statusExecucao->keys()),
            datasets: [{
                data: @json($statusExecucao->values()),
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    // 🔹 Equipes mais envolvidas
    new Chart(ctxEquipe, {
        type: 'bar',
        data: {
            labels: @json($topEquipes->pluck('nome_completo')),
            datasets: [{
                label: 'Projetos Participados',
                data: @json($topEquipes->pluck('total_projetos')),
                backgroundColor: '#17a2b8'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
@endsection
