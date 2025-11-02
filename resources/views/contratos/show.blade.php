@extends('layouts.app')

@section('title', 'Detalhes do Contrato')

@section('content')
<div class="container-fluid">

    <!-- 🔹 Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
            <li class="breadcrumb-item">
                <a href="{{ route('contratos.index') }}" class="text-decoration-none text-primary fw-semibold">
                    <i class="fas fa-file-contract me-1"></i>Contratos
                </a>
            </li>
            <li class="breadcrumb-item active text-secondary fw-semibold" aria-current="page">
                Contrato nº {{ $contrato->numero }}
            </li>
        </ol>
    </nav>

    <!-- 🔹 Detalhes do Contrato -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-contract me-2"></i>Contrato nº {{ $contrato->numero }}
            </h5>
            <a href="{{ route('empenhos.create', ['contrato_id' => $contrato->id]) }}"
               class="btn btn-light btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Cadastrar Empenho
            </a>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Empresa:</strong> {{ $contrato->contratada->razao_social ?? '-' }}</p>
                    <p><strong>CNPJ:</strong> {{ $contrato->contratada->cnpj ?? '-' }}</p>
                    <p><strong>Objeto:</strong> {{ $contrato->objeto }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Valor Global:</strong> R$ {{ number_format($contrato->valor_global, 2, ',', '.') }}</p>
                    <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}
                        a {{ \Carbon\Carbon::parse($contrato->data_fim)->format('d/m/Y') }}</p>
                    <p><strong>Situação:</strong>
                        <span class="badge
                            @if($contrato->situacao == 'Vigente') bg-success
                            @elseif($contrato->situacao == 'Encerrado') bg-danger
                            @elseif($contrato->situacao == 'Pendente') bg-warning text-dark
                            @else bg-light text-muted @endif">
                            {{ ucfirst($contrato->situacao) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔹 Empenhos vinculados -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Empenhos Vinculados
            </h6>
        </div>

        <div class="card-body">
            @if($contrato->empenhos->count() > 0)
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Número</th>
                        <th>Data</th>
                        <th>Valor (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contrato->empenhos as $emp)
                    <tr>
                        <td>{{ $emp->id }}</td>
                        <td>{{ $emp->numero }}</td>
                        <td>{{ \Carbon\Carbon::parse($emp->data_empenho)->format('d/m/Y') }}</td>
                        <td>R$ {{ number_format($emp->valor, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted mb-0">Nenhum empenho vinculado a este contrato.</p>
            @endif
        </div>
    </div>
</div>
@endsection
