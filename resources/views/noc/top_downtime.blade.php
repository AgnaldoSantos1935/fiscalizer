@extends('layouts.app')

@section('title','Top Hosts – Downtime')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0">
            <h4 class="text-secondary fw-semibold">
                <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                Top 10 Hosts com Mais Downtime
            </h4>
        </div>

        <div class="card-body">
            <canvas id="graficoDowntime" height="150"></canvas>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    fetch("{{ route('api.noc.top-downtime') }}")
        .then(r => r.json())
        .then(d => {

            new Chart(document.getElementById('graficoDowntime'), {
                type: 'bar',
                data: {
                    labels: d.map(i => i.host),
                    datasets: [{
                        label: 'Downtime (minutos)',
                        data: d.map(i => i.downtime_min),
                        backgroundColor: '#dc3545',
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });

        });

});
</script>
@endsection
