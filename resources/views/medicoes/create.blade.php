@extends('layouts.app')

@section('title', 'Cadastrar Medição')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    @section('breadcrumb')
      @include('layouts.components.breadcrumbs', [
        'trail' => [
          ['label' => 'Contratos', 'icon' => 'fas fa-file-contract', 'url' => route('contratos.index')],
          ['label' => 'Contrato ' . ($contrato->numero ?? ''), 'url' => route('contratos.show', $contrato->id)],
          ['label' => 'Cadastrar Medição']
        ]
      ])
    @endsection
    <h3 class="mb-3">Cadastrar Medição – Contrato {{ $contrato->numero }}</h3>

    <div class="card mb-3">
        <div class="card-header">
            <strong>Dados do Contrato</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Número</dt>
                        <dd class="col-sm-8">{{ $contrato->numero ?? '—' }}</dd>
                        <dt class="col-sm-4">Objeto</dt>
                        <dd class="col-sm-8">{{ $contrato->objeto ?? '—' }}</dd>
                        <dt class="col-sm-4">Contratada</dt>
                        <dd class="col-sm-8">{{ optional($contrato->contratada)->razao_social ?? ($contrato->empresa_razao_social ?? '—') }}</dd>
                        <dt class="col-sm-4">CNPJ</dt>
                        <dd class="col-sm-8">{{ optional($contrato->contratada)->cnpj ?? ($contrato->empresa_cnpj ?? '—') }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Início</dt>
                        <dd class="col-sm-8">{{ optional($contrato->data_inicio ?? $contrato->data_inicio_vigencia ?? $contrato->data_assinatura)->format('d/m/Y') ?? '—' }}</dd>
                        <dt class="col-sm-4">Fim</dt>
                        <dd class="col-sm-8">{{ optional($contrato->data_fim)->format('d/m/Y') ?? '—' }}</dd>
                        <dt class="col-sm-4">Valor Global</dt>
                        <dd class="col-sm-8">{{ $contrato->valor_global !== null ? ('R$ ' . number_format((float)$contrato->valor_global, 2, ',', '.')) : '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('contratos.medicoes.store', $contrato->id) }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Competência (MM/AAAA)</label>
                <input type="text" name="competencia" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Tipo de medição</label>
                <select name="tipo" class="form-select">
                    <option value="software"    {{ $tipo=='software'?'selected':'' }}>Fábrica de Software</option>
                    <option value="telco"       {{ $tipo=='telco'?'selected':'' }}>Internet / Links (Telco)</option>
                    <option value="fixo_mensal" {{ $tipo=='fixo_mensal'?'selected':'' }}>Valor Mensal Fixo</option>
                </select>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3" id="tipoTabs">
            <li class="nav-item">
                <button class="nav-link {{ $tipo=='software'?'active':'' }}" data-bs-toggle="tab" data-bs-target="#tabSoftware" type="button">
                    Software (PF/UST)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $tipo=='telco'?'active':'' }}" data-bs-toggle="tab" data-bs-target="#tabTelco" type="button">
                    Telco (Internet/Links)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $tipo=='fixo_mensal'?'active':'' }}" data-bs-toggle="tab" data-bs-target="#tabFixo" type="button">
                    Mensal Fixo
                </button>
            </li>
        </ul>

        <div class="tab-content border p-3 bg-white rounded-3">
            <div class="tab-pane fade {{ $tipo=='software'?'show active':'' }}" id="tabSoftware">
                @include('medicoes.partials.form_software')
            </div>
            <div class="tab-pane fade {{ $tipo=='telco'?'show active':'' }}" id="tabTelco">
                @include('medicoes.partials.form_telco')
            </div>
            <div class="tab-pane fade {{ $tipo=='fixo_mensal'?'show active':'' }}" id="tabFixo">
                @include('medicoes.partials.form_fixo')
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-end gap-2">
            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar Medição
            </button>
            <a href="{{ route('contratos.show', $contrato->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
