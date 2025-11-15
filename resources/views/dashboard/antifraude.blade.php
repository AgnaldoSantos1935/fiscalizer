@extends('layouts.app')

@section('title', 'Dashboard Antifraude – Fábrica de Software')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-4">
        <i class="fas fa-shield-alt text-danger"></i>
        Dashboard Antifraude – Fábrica de Software
    </h4>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Medições analisadas</h6>
                    <h3 class="fw-bold">{{ $cards['total_medicoes'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total de PF</h6>
                    <h3 class="fw-bold">{{ $cards['total_pf'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total de horas</h6>
                    <h3 class="fw-bold">{{ $cards['total_horas'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total de pessoas</h6>
                    <h3 class="fw-bold">{{ $cards['total_pessoas'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico PF x Horas --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="fw-semibold">
                <i class="fas fa-chart-bar text-primary me-2"></i>
                PF x Horas por Medição
            </h5>
        </div>
        <div class="card-body">
            <canvas id="pfHorasChart" height="100"></canvas>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartData = @json($chartData);

    const labels = chartData.map(i => i.label);
    const dataPf  = chartData.map(i => i.pf);
    const dataHoras = chartData.map(i => i.horas);
    const dataHorasPf = chartData.map(i => i.horas_por_pf);

    const ctx = document.getElementById('pfHorasChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'PF',
                    data: dataPf,
                    yAxisID: 'y',
                },
                {
                    label: 'Horas',
                    data: dataHoras,
                    yAxisID: 'y1',
                },
                {
                    label: 'Horas por PF',
                    data: dataHorasPf,
                    type: 'line',
                    yAxisID: 'y2',
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { position: 'left', beginAtZero: true },
                y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } },
                y2: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, display: false },
            }
        }
    });
</script>
@endsection
