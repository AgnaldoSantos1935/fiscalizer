@extends('layouts.app')

@section('title','NOC – Monitoramento de Links')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <!-- KPIs -->
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-light">
                <h6>Total de Hosts</h6>
                <h3 id="noc-total" class="fw-bold">—</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-light">
                <h6>Online</h6>
                <h3 id="noc-online" class="fw-bold text-success">—</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-light">
                <h6>Offline</h6>
                <h3 id="noc-offline" class="fw-bold text-danger">—</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-light">
                <h6>Latência Média</h6>
                <h3 id="noc-latencia" class="fw-bold text-primary">—</h3>
            </div>
        </div>

    </div>

    <!-- Gráfico de Latência Geral -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold text-secondary">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Latência Geral dos Hosts
            </h5>
        </div>
        <div class="card-body">
            <canvas id="graficoLatenciaGeral" height="120"></canvas>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// == ATUALIZAÇÃO PERIÓDICA ==
function atualizarNOC() {
    fetch("/api/hosts/status")
    .then(r => r.json())
    .then(statuses => {

        let total = statuses.length;
        let online = statuses.filter(s => s.status == 1).length;
        let offline = total - online;

        document.getElementById("noc-total").innerText = total;
        document.getElementById("noc-online").innerText = online;
        document.getElementById("noc-offline").innerText = offline;

    });

    fetch("/api/monitoramentos/latencia-geral")
        .then(r => r.json())
        .then(lat => {
            document.getElementById("noc-latencia").innerText = lat.media.toFixed(1) + ' ms';
            graficoLatencia.data.datasets[0].data = lat.series;
            graficoLatencia.update();
        });
}

setInterval(atualizarNOC, 5000);
atualizarNOC();

// == GRÁFICO ==
const ctx = document.getElementById('graficoLatenciaGeral').getContext('2d');

let graficoLatencia = new Chart(ctx, {
    type: 'line',
    data: {
        labels: Array(20).fill(""),
        datasets: [{
            label: "Latência Média",
            data: [],
            borderWidth: 2,
            borderColor: "#007bff"
        }]
    },
});
</script>
@endsection
