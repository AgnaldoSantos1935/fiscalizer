@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="card">
    <div class="card-body">
        <canvas id="graficoUptime"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('graficoUptime');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($estatisticas->pluck('nome')),
        datasets: [{
            label: 'Uptime (%)',
            data: @json($estatisticas->pluck('uptime')),
            borderWidth: 1,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, max: 100 }
        },
        plugins: {
            legend: { display: false }
        }
    }
});
</script>
@stop
