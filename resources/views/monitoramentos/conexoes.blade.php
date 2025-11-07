@extends('layouts.app')

@section('title', 'Monitoramento de Conexões')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="small-box bg-success text-white p-3 rounded-4 shadow">
        <h3>{{ $resumo['online'] }}</h3>
        <p>Conexões Online</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="small-box bg-danger text-white p-3 rounded-4 shadow">
        <h3>{{ $resumo['offline'] }}</h3>
        <p>Conexões Offline</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="small-box bg-secondary text-white p-3 rounded-4 shadow">
        <h3>{{ $resumo['total'] }}</h3>
        <p>Total de Hosts</p>
      </div>
    </div>
  </div>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-0">
      <h5 class="text-secondary fw-semibold">
        <i class="fas fa-chart-line text-primary me-2"></i>Histórico de Monitoramento
      </h5>
    </div>
    <div class="card-body">
      <canvas id="graficoUptime" height="120"></canvas>
    </div>
  </div>

  <div class="card mt-4 shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-0">
      <h5 class="text-secondary fw-semibold">
        <i class="fas fa-list me-2 text-primary"></i>Últimos Registros
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>Data/Hora</th>
            <th>Host</th>
            <th>IP</th>
            <th>Status</th>
            <th>Tempo (ms)</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($ultimos as $log)
          <tr>
            <td>{{ \Carbon\Carbon::parse($log->verificado_em)->format('d/m/Y H:i:s') }}</td>
            <td>{{ $log->host->nome_conexao ?? '—' }}</td>
            <td>{{ $log->ip }}</td>
            <td>
              <span class="badge bg-{{ $log->status == 'online' ? 'success' : 'danger' }}">
                {{ strtoupper($log->status) }}
              </span>
            </td>
            <td>{{ $log->tempo_resposta ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  fetch('/api/hosts/monitoramentos')
    .then(resp => resp.json())
    .then(data => {
      const ctx = document.getElementById('graficoUptime').getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Online (%)',
            data: data.uptime,
            borderColor: 'rgb(75, 192, 192)',
            fill: false,
            tension: 0.1
          }]
        }
      });
    });
});
</script>
@endsection
