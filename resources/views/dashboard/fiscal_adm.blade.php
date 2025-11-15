@extends('layouts.app')

@section('title', 'Desempenho do Fiscal Administrativo')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-4">
        <i class="fas fa-chart-line text-primary"></i>
        Painel do Fiscal Administrativo
    </h4>

    <div class="row">

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Documentos Enviados</h6>
                    <h3 class="fw-bold">{{ $stats['docs_enviados'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Medições Finalizadas</h6>
                    <h3 class="fw-bold">{{ $stats['medicoes_finalizadas'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Inconsistências Encontradas</h6>
                    <h3 class="fw-bold text-danger">{{ $stats['inconsistencias'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Tempo Médio por Medição</h6>
                    <h3 class="fw-bold">{{ $stats['tempo_medio'] }} h</h3>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
