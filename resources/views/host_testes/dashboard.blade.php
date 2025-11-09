@extends('layouts.app')

@section('title', 'Dashboard de Conectividade')

@section('content_header')
<h1><i class="fas fa-chart-pie me-2 text-primary"></i> Dashboard de Conectividade</h1>
@stop

@section('content')
<div class="container-fluid">

  <!-- 🔹 Filtros -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
      <form id="filtros" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label fw-semibold">Período Inicial</label>
          <input type="date" name="inicio" class="form-control" value="{{ now()->subDays(30)->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Período Final</label>
          <input type="date" name="fim" class="form-control" value="{{ now()->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Provedor</label>
          <select name="provedor" class="form-select">
            <option value="">Todos</option>
            @foreach($provedores as $p)
              <option value="{{ $p }}">{{ $p }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 text-end">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-sync me-1"></i> Atualizar
          </button>
        </div>
      </form>
    </div>
  </div>

  <div id="cards" class="row g-4 mb-4"></div>

  <div class="row g-4">
    <div class="col-md-4"><canvas id="graficoStatus"></canvas></div>
    <div class="col-md-4"><canvas id="graficoLatencia"></canvas></div>
    <div class="col-md-4"><canvas id="graficoPerda"></canvas></div>
  </div>

  <div class="row g-4 mt-4">
    <div class="col-md-12">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 fw-semibold">
          <i class="fas fa-tachometer-alt text-warning me-2"></i> Top 10 Conexões Mais Lentas
        </div>
        <div class="card-body">
          <canvas id="graficoTopLentos"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 🔹 Modal de Histórico -->
<div class="modal fade" id="modalHistorico" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-clock me-2"></i>Histórico de Conectividade</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3" id="hostNome"></p>
        <canvas id="graficoHistorico"></canvas>
      </div>
    </div>
  </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const colorMap = {
  'Starlink': '#0d6efd','Vivo': '#6f42c1','Claro': '#dc3545','Oi': '#ffc107',
  'TIM': '#198754','HughesNet': '#20c997','Prodepa': '#0dcaf0','default': '#adb5bd'
};
const getColor = (p) => colorMap[p] || colorMap.default;

let chartStatus, chartLat, chartPerda, chartTop, chartHistorico;

async function carregarDashboard() {
  const params = new URLSearchParams(new FormData(document.getElementById('filtros'))).toString();
  const res = await fetch(`{{ route('hosts.dashboard.data') }}?${params}`);
  const dados = await res.json();

  document.getElementById('cards').innerHTML = `
    <div class="col-md-3"><div class="card text-center p-3 shadow-sm"><h6>Total</h6><h3>${dados.total}</h3></div></div>
    <div class="col-md-3"><div class="card text-center p-3 shadow-sm"><h6>Ativos</h6><h3 class="text-success">${dados.ativos}</h3></div></div>
    <div class="col-md-3"><div class="card text-center p-3 shadow-sm"><h6>Latência Média</h6><h3 class="text-info">${dados.latencia} ms</h3></div></div>
    <div class="col-md-3"><div class="card text-center p-3 shadow-sm"><h6>Perda Média</h6><h3 class="text-danger">${dados.perda}%</h3></div></div>
  `;

  [chartStatus, chartLat, chartPerda, chartTop].forEach(c => c?.destroy());

  chartStatus = new Chart(graficoStatus, {
    type: 'doughnut',
    data: { labels: Object.keys(dados.statusDist),
      datasets: [{ data: Object.values(dados.statusDist),
        backgroundColor: ['#198754','#dc3545','#6c757d'] }] },
    options: { plugins: { legend: { position: 'bottom' } } }
  });

  chartLat = new Chart(graficoLatencia, {
    type: 'bar',
    data: { labels: dados.latPorProv.map(d => d.provedor),
      datasets: [{ label: 'Latência Média (ms)',
        data: dados.latPorProv.map(d => d.media),
        backgroundColor: dados.latPorProv.map(d => getColor(d.provedor)) }] },
    options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
  });

  chartPerda = new Chart(graficoPerda, {
    type: 'bar',
    data: { labels: dados.perdaPorProv.map(d => d.provedor),
      datasets: [{ label: 'Perda de Pacotes (%)',
        data: dados.perdaPorProv.map(d => d.media),
        backgroundColor: dados.perdaPorProv.map(d => getColor(d.provedor)) }] },
    options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
  });

  chartTop = new Chart(graficoTopLentos, {
    type: 'bar',
    data: { labels: dados.topLentos.map(d => d.nome_conexao),
      datasets: [{ label: 'Latência (ms)',
        data: dados.topLentos.map(d => d.media),
        backgroundColor: dados.topLentos.map((d,i)=> i<3?'#dc3545':'#ffc107') }] },
    options: {
      indexAxis: 'y',
      onClick: (evt, els) => {
        if (els.length>0){ const i=els[0].index; abrirHistorico(dados.topLentos[i].id,dados.topLentos[i].nome_conexao); }
      },
      plugins:{legend:{display:false}},scales:{x:{beginAtZero:true}}
    }
  });
}

async function abrirHistorico(id,nome){
  const modal = new bootstrap.Modal(document.getElementById('modalHistorico'));
  document.getElementById('hostNome').innerText = `Conexão: ${nome}`;
  modal.show();

  const res = await fetch(`{{ url('hosts/dashboard/historico') }}/${id}`);
  const dados = await res.json();
  if (chartHistorico) chartHistorico.destroy();

  chartHistorico = new Chart(graficoHistorico, {
    type: 'line',
    data: { labels: dados.map(d=>d.data),
      datasets: [
        { label:'Latência Média (ms)',data:dados.map(d=>d.latencia_media),borderColor:'#0dcaf0',yAxisID:'y' },
        { label:'Perda de Pacotes (%)',data:dados.map(d=>d.perda_media),borderColor:'#dc3545',yAxisID:'y' },
        { label:'Disponibilidade (%)',data:dados.map(d=>d.uptime_percent),borderColor:'#198754',yAxisID:'y1' }
      ] },
    options: {
      responsive:true,interaction:{mode:'index',intersect:false},stacked:false,
      plugins:{legend:{position:'bottom'}},
      scales:{ y:{beginAtZero:true},y1:{beginAtZero:true,position:'right',grid:{drawOnChartArea:false}} }
    }
  });
}

document.getElementById('filtros').addEventListener('submit',e=>{e.preventDefault();carregarDashboard();});
document.addEventListener('DOMContentLoaded', carregarDashboard);
</script>
@stop
