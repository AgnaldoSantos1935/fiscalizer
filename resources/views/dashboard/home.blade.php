@extends('layouts.app')

@section('title', 'Dashboard - Fiscalizer')

@section('content')
<div class="container-fluid">

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
