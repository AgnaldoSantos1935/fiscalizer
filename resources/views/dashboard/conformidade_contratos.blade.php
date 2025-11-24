@extends('layouts.app')

@section('title', 'Conformidade Contratual')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <h3 class="mb-4">
        <i class="fas fa-balance-scale text-primary"></i>
        Conformidade e Risco dos Contratos
    </h3>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total de Contratos</h6>
                    <h3 class="fw-bold">{{ $total }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Score Médio de Risco</h6>
                    <h3 class="fw-bold">{{ number_format($mediaScore, 1, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Contratos Críticos (score &lt; 40)</h6>
                    <h3 class="fw-bold text-danger">{{ $totalCriticos }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico por nível de risco --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-pie text-primary me-2"></i>
                Distribuição por Nível de Risco
            </h5>
        </div>
        <div class="card-body">
            <canvas id="riscoChart" height="80"></canvas>
        </div>
    </div>

    {{-- Tabela de contratos mais críticos --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                Top 10 contratos mais críticos
            </h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Objeto</th>
                        <th>Empresa</th>
                        <th>Score</th>
                        <th>Nível</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criticos as $c)
                    <tr>
                        <td>
                            <a href="{{ route('contratos.show', $c->id) }}">
                                {{ $c->numero }}
                            </a>
                        </td>
                        <td>{{ Str::limit($c->objeto, 80) }}</td>
                        <td>{{ $c->empresa_razao_social }}</td>
                        <td>{{ $c->risco_score }}</td>
                        <td>{{ $c->risco_nivel }}</td>
                    </tr>
                    @endforeach
                    @if(!count($criticos))
                    <tr>
                        <td colspan="5" class="text-center text-muted p-3">
                            Nenhum contrato avaliado ainda.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const porNivel = @json($porNivel);
    const labels = porNivel.map(i => i.risco_nivel ?? 'N/I');
    const valores = porNivel.map(i => i.total);

    const ctx = document.getElementById('riscoChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Quantidade de contratos',
                data: valores,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
