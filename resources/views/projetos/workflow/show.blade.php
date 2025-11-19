@extends('layouts.app')

@section('title', 'Workflow do Projeto')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="fas fa-project-diagram text-primary me-2"></i>
            Workflow do Projeto: {{ $projeto->nome ?? $projeto->titulo ?? 'Projeto '.$projeto->id }}
        </h4>

        <a href="{{ route('projetos.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Card de status -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <p class="mb-1 text-muted small">Status do processo</p>
                <h5 class="mb-0">
                    @if($instancia)
                        @switch($instancia->status)
                            @case('em_execucao')
                                <span class="badge bg-primary">Em execução</span>
                                @break
                            @case('concluido')
                                <span class="badge bg-success">Concluído</span>
                                @break
                            @case('cancelado')
                                <span class="badge bg-danger">Cancelado</span>
                                @break
                        @endswitch
                    @else
                        <span class="badge bg-secondary">Não iniciado</span>
                    @endif
                </h5>
            </div>

            <div>
                @if(!$instancia)
                    <form action="{{ route('projetos.workflow.iniciar', $projeto->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play me-1"></i> Iniciar workflow
                        </button>
                    </form>
                @else
                    <form action="{{ route('projetos.workflow.avancar', $projeto->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-step-forward me-1"></i> Avançar etapa
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if($instancia)
        <!-- Linha do tempo das etapas -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 text-secondary fw-semibold">
                    <i class="fas fa-stream me-2 text-primary"></i>Etapas do processo
                </h5>
            </div>
            <div class="card-body">

                <div class="row">
                    @foreach($instancia->etapas()->with('etapa')->orderBy('id')->get() as $instEtapa)
                        @php
                            $statusClass = match($instEtapa->status) {
                                'concluida'   => 'bg-success',
                                'em_execucao' => 'bg-primary',
                                'pendente'    => 'bg-light border',
                                'atrasada'    => 'bg-danger',
                                default       => 'bg-light border',
                            };
                        @endphp

                        <div class="col-md-3 mb-3">
                            <div class="card {{ $statusClass }} text-white h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        {{ $instEtapa->etapa->ordem }}. {{ $instEtapa->etapa->nome }}
                                    </h6>
                                    <p class="small mb-1">
                                        <strong>Status:</strong> {{ ucfirst($instEtapa->status) }}
                                    </p>
                                    <p class="small mb-1">
                                        <strong>Início:</strong>
                                        {{ $instEtapa->data_inicio ? $instEtapa->data_inicio->format('d/m/Y H:i') : '—' }}
                                    </p>
                                    <p class="small mb-1">
                                        <strong>Fim:</strong>
                                        {{ $instEtapa->data_fim ? $instEtapa->data_fim->format('d/m/Y H:i') : '—' }}
                                    </p>
                                    @if($instEtapa->observacoes)
                                        <p class="small mb-0">
                                            <strong>Obs.:</strong> {{ Str::limit($instEtapa->observacoes, 80) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    @endif
</div>
@endsection
