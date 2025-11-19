@extends('layouts.app')

@section('title', 'Dashboard do Projeto - '.$projeto->nome)

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

  <!-- KPIs -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 p-3 bg-light">
        <h6 class="mb-1 text-secondary">PF Total (boletins)</h6>
        <h3 id="kpiPf" class="fw-bold text-primary">
          {{ $projeto->boletins->sum('total_pf') ?? 0 }}
        </h3>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 p-3 bg-light">
        <h6 class="mb-1 text-secondary">UST Total (boletins)</h6>
        <h3 id="kpiUst" class="fw-bold text-success">
          {{ $projeto->boletins->sum('total_ust') ?? 0 }}
        </h3>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 p-3 bg-light">
        <h6 class="mb-1 text-secondary">Horas lançadas</h6>
        <h3 id="kpiHoras" class="fw-bold text-info">
          {{ $projeto->atividades->sum('horas') ?? 0 }}
        </h3>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 p-3 bg-light">
        <h6 class="mb-1 text-secondary">% Progresso (PF)</h6>
        @php
          $pfPlanejado = $projeto->apfs->sum('total_pf') ?: 0;
          $pfEntregue  = $projeto->boletins->sum('total_pf') ?: 0;
          $perc = $pfPlanejado > 0 ? round(($pfEntregue / $pfPlanejado)*100, 1) : 0;
        @endphp
        <h3 class="fw-bold {{ $perc >= 80 ? 'text-success' : 'text-warning' }}">
          {{ $perc }}%
        </h3>
      </div>
    </div>
  </div>

  <!-- Gráfico PF/UST -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0">
      <h5 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-chart-line text-primary me-2"></i>
        Evolução PF / UST por Boletim
      </h5>
    </div>
    <div class="card-body">
      <canvas id="graficoPfUst" height="120"></canvas>
    </div>
  </div>

  <!-- Gráfico Esforço -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0">
      <h5 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-user-clock text-primary me-2"></i>
        Esforço (horas) por mês
      </h5>
    </div>
    <div class="card-body">
      <canvas id="graficoEsforco" height="120"></canvas>
    </div>
  </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {

  // PF/UST
  fetch("{{ route('api.projetos.dashboard.pf_ust', $projeto->id) }}")
    .then(r => r.json())
    .then(d => {
      new Chart(document.getElementById('graficoPfUst').getContext('2d'), {
        type: 'line',
        data: {
          labels: d.labels,
          datasets: [
            { label: 'PF',  data: d.pf,  borderWidth: 2 },
            { label: 'UST', data: d.ust, borderWidth: 2 },
          ]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });
    });

  // Esforço
  fetch("{{ route('api.projetos.dashboard.esforco', $projeto->id) }}")
    .then(r => r.json())
    .then(d => {
      new Chart(document.getElementById('graficoEsforco').getContext('2d'), {
        type: 'bar',
        data: {
          labels: d.labels,
          datasets: [{ label: 'Horas', data: d.horas, borderWidth: 1 }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true } }
        }
      });
    });

});
</script>
@endsection
