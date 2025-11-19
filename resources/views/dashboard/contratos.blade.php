@extends('layouts.app')

@section('title', 'Dashboard de Contratos')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <h3 class="mb-4">
        <i class="fas fa-file-contract text-primary"></i>
        Situação e Risco dos Contratos
    </h3>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total de Contratos</h6>
                    <h3 class="fw-bold">{{ $total }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Ativos</h6>
                    <h3 class="fw-bold text-success">{{ $ativos }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Encerrados</h6>
                    <h3 class="fw-bold text-secondary">{{ $encerrados }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Contratos com alto risco</h6>
                    <h3 class="fw-bold text-danger">{{ $altoRisco }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico por modalidade --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-pie text-primary me-2"></i>
                Distribuição por Modalidade
            </h5>
        </div>
        <div class="card-body">
            <canvas id="modalidadeChart" height="80"></canvas>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const modalidades = @json($porModalidade->pluck('modalidade'));
    const valores = @json($porModalidade->pluck('total'));

    const ctx = document.getElementById('modalidadeChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: modalidades,
            datasets: [{
                data: valores,
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endsection
