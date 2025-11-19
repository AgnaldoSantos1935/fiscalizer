@extends('layouts.app')
@section('title', 'Projeto: '.$projeto->titulo)

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-secondary">
                <i class="fas fa-project-diagram text-primary me-2"></i>
                Projeto: {{ $projeto->codigo }} — {{ $projeto->titulo }}
            </h4>
            <div>
                <a href="{{ route('projetos.edit', $projeto->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-pen me-1"></i> Editar
                </a>
                <a href="{{ route('projetos.index') }}" class="btn btn-outline-primary btn-sm ms-1">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body bg-white">

            <div class="row mb-3">
                <div class="col-md-4">
                    <p><strong>Contrato:</strong> {{ $projeto->contrato->numero ?? '—' }}</p>
                    <p><strong>Item:</strong> {{ $projeto->itemContrato->descricao ?? '—' }}</p>
                    <p><strong>Sistema:</strong> {{ $projeto->sistema ?? '—' }}</p>
                    <p><strong>Módulo:</strong> {{ $projeto->modulo ?? '—' }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Gerente Técnico:</strong> {{ $projeto->gerenteTecnico->name ?? '—' }}</p>
                    <p><strong>Gerente Adm.:</strong> {{ $projeto->gerenteAdm->name ?? '—' }}</p>
                    <p><strong>DRE:</strong> {{ $projeto->dre->nome ?? '—' }}</p>
                    <p><strong>Escola:</strong> {{ $projeto->escola->nome ?? '—' }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Período:</strong> {{ $projeto->data_inicio }} a {{ $projeto->data_fim }}</p>
                    <p><strong>PF Planejado:</strong> {{ $projeto->pf_planejado }}</p>
                    <p><strong>PF Entregue:</strong> {{ $projeto->pf_entregue }}</p>
                    <p><strong>Prioridade:</strong> {{ ucfirst($projeto->prioridade) }}</p>
                </div>
            </div>

            <hr>

            <h5 class="fw-semibold mb-2">Descrição / Escopo</h5>
            <p>{{ $projeto->descricao ?? '—' }}</p>

        </div>
    </div>

    {{-- Aqui você pode incluir as abas com partials:
        @include('projetos.partials.apf')
        @include('projetos.partials.atividades')
        @include('projetos.partials.medicoes')
        @include('projetos.partials.boletins')
    --}}
</div>
@endsection
