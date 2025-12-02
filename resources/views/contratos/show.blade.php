@extends('layouts.app')

@section('title', 'Contrato nº '.$contrato->numero)

@section('content_header_title', 'Contratos')
@section('content_header_subtitle', 'Detalhes do Contrato')

@section('breadcrumb')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
        <li class="breadcrumb-item">
            <a href="{{ route('contratos.index') }}" class="text-primary text-decoration-none fw-semibold">
                <i class="fas fa-file-contract me-1"></i> Contratos
            </a>
        </li>
        <li class="breadcrumb-item active text-secondary fw-semibold">
            Contrato nº {{ $contrato->numero }}
        </li>
    </ol>
</nav>
@endsection

@section('content_body')
<div class="container-fluid">

    {{-- ======================= --}}
    {{--  MENSAGENS DE ALERTA  --}}
    {{-- ======================= --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif



    {{-- ======================= --}}
    {{--    RESUMO FINANCEIRO    --}}
    {{-- ======================= --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="fw-semibold mb-0">
                <i class="fas fa-coins me-2 text-primary"></i>Resumo Financeiro
            </h5>
        </div>

        <div class="card-body pb-2">
            <div class="row g-3">

                {{-- Valor Global --}}
                <div class="col-md-3">
                    <div class="small-box bg-primary text-white shadow-sm rounded-3">
                        <div class="inner">
                            <h4>{{ $totais['valor_global_br'] }}</h4>
                            <p class="mb-0">Valor Global</p>
                        </div>
                        <div class="icon"><i class="fas fa-layer-group"></i></div>
                    </div>
                </div>

                {{-- Empenhado --}}
                <div class="col-md-3">
                    <div class="small-box bg-info text-white shadow-sm rounded-3">
                        <div class="inner">
                            <h4>{{ $totais['valor_empenhado_br'] }}</h4>
                            <p class="mb-0">Empenhado</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    </div>
                </div>

                {{-- Pago --}}
                <div class="col-md-3">
                    <div class="small-box bg-success text-white shadow-sm rounded-3">
                        <div class="inner">
                            <h4>{{ $totais['valor_pago_br'] }}</h4>
                            <p class="mb-0">Pago</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>

                {{-- Saldo --}}
                <div class="col-md-3">
                    <div class="small-box bg-warning text-dark shadow-sm rounded-3">
                        <div class="inner">
                            <h4>{{ $totais['saldo_br'] }}</h4>
                            <p class="mb-0">Saldo Disponível</p>
                        </div>
                        <div class="icon"><i class="fas fa-balance-scale"></i></div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    {{-- ======================= --}}
    {{--     DADOS DO CONTRATO   --}}
    {{-- ======================= --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="fw-semibold mb-0">
                <i class="fas fa-info-circle me-2 text-primary"></i>Dados do Contrato
            </h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-6">
                    <dl class="row">

                        <dt class="col-sm-4">Situação</dt>
                        <dd class="col-sm-8">{{ optional($contrato->situacaoContrato)->nome }}</dd>

                        <dt class="col-sm-4">Fiscais</dt>
                        <dd class="col-sm-8">
                            Tec: {{ optional($contrato->fiscalTecnico)->nome_completo ?: '—' }}<br>
                            Adm: {{ optional($contrato->fiscalAdministrativo)->nome_completo ?: '—' }}
                        </dd>

                        <dt class="col-sm-4">Gestor</dt>
                        <dd class="col-sm-8">{{ optional($contrato->gestor)->nome_completo ?: '—' }}</dd>

                        <dt class="col-sm-4">Número</dt>
                        <dd class="col-sm-8">{{ $contrato->numero }}</dd>

                        <dt class="col-sm-4">Processo</dt>
                        <dd class="col-sm-8">{{ isset($contrato->processo_origem) ? $contrato->processo_origem : '—' }}</dd>

                        <dt class="col-sm-4">Modalidade</dt>
                        <dd class="col-sm-8">{{ isset($contrato->modalidade) ? $contrato->modalidade : '—' }}</dd>

                        <dt class="col-sm-4">Assinatura</dt>
                        <dd class="col-sm-8">{{ isset($contrato->data_assinatura) ? $contrato->data_assinatura : '—' }}</dd>

                        <dt class="col-sm-4">Vigência</dt>
                        <dd class="col-sm-8">
                            {{ isset($totais['vigencia_periodo']) ? $totais['vigencia_periodo'] : '—' }}
                        </dd>

                    </dl>
                </div>

                <div class="col-md-6">

                    <h6 class="fw-bold text-muted">Objeto</h6>
                    <p>{{ $contrato->objeto }}</p>

                    <h6 class="fw-bold text-muted">Contratada</h6>
                    <p class="mb-0">
                        {{ optional($contrato->contratada)->razao_social ?: '—' }}<br>
                        CNPJ: {{ optional($contrato->contratada)->cnpj ?: '—' }} <br>
                        Email: {{ optional($contrato->contratada)->email ?: '—' }} <br>
                        Representante: {{ isset($contrato->empresa_representante) ? $contrato->empresa_representante : '—' }}
                    </p>

                </div>

            </div>
        </div>
    </div>



    {{-- ======================= --}}
    {{--     ITENS CONTRATADOS   --}}
    {{-- ======================= --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between">
            <h5 class="fw-semibold mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Itens Contratados</h5>

            <a href="{{ route('contratos.itens', $contrato->id) }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Inserir Item
            </a>
        </div>

        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Unid.</th>
                        <th class="text-end">Qtd</th>
                        <th class="text-end">Meses</th>
                        <th class="text-end">Unitário</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                @foreach(($itens ?? []) as $it)
                    @php
                        $q  = (float) ($it->quantidade ?? 0);
                        $vu = (float) ($it->valor_unitario ?? 0);
                        $m  = (int)   ($it->meses ?? 0);
                        $m  = $m > 0 ? $m : 1;
                        $tot = $q * $vu * $m;
                    @endphp
                    <tr>
                        <td>{{ $it->descricao_item }}</td>
                        <td>{{ $it->unidade_medida }}</td>
                        <td class="text-end">{{ number_format($q, 2, ',', '.') }}</td>
                        <td class="text-end">{{ $m }}</td>
                        <td class="text-end">R$ {{ number_format($vu, 2, ',', '.') }}</td>
                        <td class="text-end">R$ {{ number_format($tot, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>



    {{-- ======================= --}}
    {{--     DOCUMENTOS          --}}
    {{-- ======================= --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between">
            <h5 class="fw-semibold mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Documentos</h5>

            @can('contratos.anexar_documento')
            <a href="{{ route('contratos.documentos.create', $contrato->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-upload"></i> Cadastrar Documento
            </a>
            @endcan
        </div>

        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Upload</th>
                        <th>Arquivo</th>
                    </tr>
                </thead>
                <tbody>
                @foreach(($contrato->documentos ?? []) as $d)
                    <tr>
                        <td>{{ $d->titulo ?? '—' }}</td>
                        <td>{{ optional($d->documentoTipo)->nome ?? ($d->tipo ?? '—') }}</td>
                        <td>{{ optional($d->created_at)->format('d/m/Y') ?? ($d->data_upload ?? '—') }}</td>
                        <td>
                            @php $path = $d->caminho_arquivo ?? null; @endphp
                            @if($path)
                                <a href="{{ route('documentos.visualizar', $d->id) }}" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-eye"></i> Abrir
                                </a>
                                <a href="{{ route('documentos.download', $d->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>



    {{-- ======================= --}}
    {{--     EMPENHOS            --}}
    {{-- ======================= --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between">
            <h5 class="fw-semibold mb-0"><i class="fas fa-file-invoice me-2 text-primary"></i>Empenhos Vinculados</h5>

            <a href="{{ route('empenhos.create') }}?contrato_id={{ $contrato->id }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Cadastrar Empenho
            </a>
        </div>

        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th class="text-end">Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach(($empenhos ?? []) as $e)
                    <tr>
                        <td>{{ $e->numero ?? '—' }}</td>
                        <td class="text-end">R$ {{ number_format((float)($e->valor_empenhado ?? 0), 2, ',', '.') }}</td>
                        <td>{{ $e->status ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>



    {{-- ======================= --}}
    {{--     PAGAMENTOS          --}}
    {{-- ======================= --}}
    <div class="card shadow-sm rounded-4">
        <div class="card-header bg-white border-0">
            <h5 class="fw-semibold mb-0"><i class="fas fa-money-bill me-2 text-success"></i>Pagamentos</h5>
        </div>

        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Empenho</th>
                        <th>Documento</th>
                        <th class="text-end">Valor</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                @foreach(($pagamentos ?? []) as $p)
                    <tr>
                        <td>{{ optional($p->empenho)->numero ?? '—' }}</td>
                        <td>{{ $p->documento ?? '—' }}</td>
                        <td class="text-end">R$ {{ number_format((float)($p->valor_pago ?? 0), 2, ',', '.') }}</td>
                        <td>{{ optional($p->data_pagamento)->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>



</div>
@endsection
