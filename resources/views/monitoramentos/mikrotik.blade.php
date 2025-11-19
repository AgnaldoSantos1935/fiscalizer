@extends('layouts.app')

@section('title','Tráfego Mikrotik (RX/TX)')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-network-wired me-2 text-primary"></i> Tráfego Mikrotik – RX/TX
            </h4>
            <form id="formHost" class="d-flex gap-2">
                <select id="selectHost" class="form-select form-select-sm">
                    @foreach($hosts as $host)
                        <option value="{{ $host->id }}">{{ $host->nome_conexao }} ({{ $host->host_alvo }})</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body">
            <canvas id="graficoMikrotik" height="120"></canvas>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chart;

function carregarGrafico(hostId) {
    fetch(`/api/monitoramentos/mikrotik/${hostId}`)
        .then(r => r.json())
        .then(d => {
            if (!chart) {
                const ctx = document.getElementById('graficoMikrotik').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: d.labels,
                        datasets: [
                            {
                                label: 'RX (bps)',
                                data: d.rx,
                                borderWidth: 2
                            },
                            {
                                label: 'TX (bps)',
                                data: d.tx,
                                borderWidth: 2
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            } else {
                chart.data.labels = d.labels;
                chart.data.datasets[0].data = d.rx;
                chart.data.datasets[1].data = d.tx;
                chart.update();
            }
        });
}

document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById('selectHost');
    carregarGrafico(select.value);

    select.addEventListener('change', function () {
        carregarGrafico(this.value);
    });

    // opcional: auto-refresh
    setInterval(() => carregarGrafico(select.value), 10000);
});
</script>
@endsection
