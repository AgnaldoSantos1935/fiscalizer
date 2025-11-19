@extends('layouts.app')

@section('title', 'Comparação da Medição')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-balance-scale text-primary me-2"></i>
            Comparação da Medição #{{ $medicao->id }}
        </h4>

        <a href="{{ route('medicoes.workflow.show', $medicao->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    {{-- Resumo geral --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <h5 class="fw-semibold mb-3">
                <i class="fas fa-clipboard-check me-2 text-primary"></i>
                Resultado Geral da Validação
            </h5>

            @php
                $resultado = $resultadoValidacao; // virá do Controller
                $classe = [
                    'aprovado'  => 'bg-success text-white',
                    'reprovado' => 'bg-danger text-white',
                    'alerta'    => 'bg-warning text-dark',
                ][$resultado['status']];
            @endphp

            <div class="p-3 rounded-3 {{ $classe }}">
                <strong>Status:</strong> {{ strtoupper($resultado['status']) }} <br>
                <strong>Resumo:</strong> {{ $resultado['mensagem'] }}
            </div>
        </div>
    </div>


    {{-- COMPARAÇÃO SIDE BY SIDE --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="fw-semibold mb-0">
                <i class="fas fa-columns me-2 text-primary"></i>
                Comparação dos Valores
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">

                    <thead class="table-light">
                        <tr>
                            <th>Origem</th>
                            <th>Valor (R$)</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        {{-- CONTRATO --}}
                        <tr>
                            <td class="fw-bold">Contrato</td>
                            <td>R$ {{ number_format($contrato->valor_global, 2, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-secondary">Referência</span>
                            </td>
                        </tr>

                        {{-- MEDIÇÃO --}}
                        @php
                            $diffMedicao = $medicao->valor_total - $contrato->valor_global;
                        @endphp

                        <tr>
                            <td class="fw-bold">Medição</td>
                            <td>R$ {{ number_format($medicao->valor_total, 2, ',', '.') }}</td>
                            <td>
                                @if($medicao->valor_total > $contrato->valor_global)
                                    <span class="badge bg-danger">
                                        Acima do contrato: R$ {{ number_format($diffMedicao, 2, ',', '.') }}
                                    </span>
                                @elseif($medicao->valor_total < $contrato->valor_global)
                                    <span class="badge bg-warning text-dark">
                                        Abaixo do contrato: R$ {{ number_format($diffMedicao, 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                        </tr>

                        {{-- PLANILHA DE MEDIÇÃO --}}
                        @php
                            $diffPlanilha = $valorPlanilha - $medicao->valor_total;
                        @endphp

                        <tr>
                            <td class="fw-bold">Planilha de Medição</td>
                            <td>R$ {{ number_format($valorPlanilha, 2, ',', '.') }}</td>
                            <td>
                                @if($valorPlanilha != $medicao->valor_total)
                                    <span class="badge bg-danger">
                                        Diferença: R$ {{ number_format($diffPlanilha, 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                        </tr>

                        {{-- NOTA FISCAL --}}
                        @php
                            $nf = $notaFiscal;
                            $diffNF = $nf->valor - $medicao->valor_total;
                        @endphp

                        <tr>
                            <td class="fw-bold">Nota Fiscal</td>
                            <td>R$ {{ number_format($nf->valor, 2, ',', '.') }}</td>
                            <td>
                                @if($nf->status == 'valido')
                                    <span class="badge bg-success">NF Válida</span>
                                @else
                                    <span class="badge bg-danger">
                                        {{ $nf->mensagem }}
                                    </span>
                                @endif
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- COMPARAÇÃO DETALHADA DOS CAMPOS --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
            <h5 class="fw-semibold mb-0">
                <i class="fas fa-search me-2 text-primary"></i>
                Detalhamento das Inconsistências
            </h5>
        </div>

        <div class="card-body">

            @if(count($inconsistencias) == 0)
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Nenhuma inconsistência encontrada.
                </div>
            @else
                <ul class="list-group">
                    @foreach($inconsistencias as $inc)
                        <li class="list-group-item list-group-item-danger mb-2">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ $inc }}
                        </li>
                    @endforeach
                </ul>
            @endif

        </div>
    </div>

</div>
@endsection
