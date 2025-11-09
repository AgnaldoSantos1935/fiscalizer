@extends('layouts.app')

@section('title', 'Detalhes da Conexão')

@section('content_header')
    <h1>
        <i class="fas fa-network-wired me-2 text-primary"></i>
        Detalhes da Conexão
    </h1>
@stop

@section('content')
    <div class="container-fluid">

        <!-- 🔹 Informações Gerais -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-secondary fw-semibold">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Informações Gerais da Conexão
                </h5>
                <div>
                    <a href="{{ route('hosts.edit', $host->id) }}" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="{{ route('hosts.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-4">
                    <!-- Nome -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Nome da Conexão</h6>
                        <p class="fw-semibold text-dark mb-0">{{ $host->nome_conexao ?? '—' }}</p>
                        <small class="text-muted">{{ $host->descricao ?? '' }}</small>
                    </div>

                    <!-- Provedor -->
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Provedor</h6>
                        <p class="fw-semibold text-dark mb-0">{{ $host->provedor ?? '—' }}</p>
                    </div>

                    <!-- Tecnologia -->
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Tecnologia</h6>
                        <p class="fw-semibold text-dark mb-0 text-capitalize">{{ $host->tecnologia ?? '—' }}</p>
                    </div>

                    <!-- IP -->
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Endereço IP</h6>
                        <p class="fw-semibold text-dark mb-0">
                            <i class="fas fa-network-wired me-1 text-secondary"></i>{{ $host->ip_atingivel ?? '—' }}
                        </p>
                    </div>

                    <!-- Porta -->
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Porta</h6>
                        <p class="fw-semibold text-dark mb-0">{{ $host->porta ?? '—' }}</p>
                    </div>

                    <!-- Status -->
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Status</h6>
                        @php
                            $badge = 'secondary';
                            if ($host->status === 'ativo') {
                                $badge = 'success';
                            } elseif ($host->status === 'inativo') {
                                $badge = 'danger';
                            } elseif ($host->status === 'em manutenção') {
                                $badge = 'warning';
                            }
                        @endphp
                        <span class="badge bg-{{ $badge }} px-3 py-2 text-uppercase">
                            <i class="fas fa-circle me-1"></i>{{ $host->status }}
                        </span>
                    </div>

                    <!-- Escola -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Local (Escola / Setor)</h6>
                        <p class="fw-semibold text-dark mb-0">{{ $host->escola->nome ?? '—' }}</p>
                        <small class="text-muted">
                            {{ $host->escola->municipio ?? '' }} –
                            {{ $host->escola->dre->nome ?? 'DRE não informada' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔹 Relações Contratuais -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-secondary fw-semibold">
                    <i class="fas fa-file-contract me-2 text-primary"></i>
                    Informações Contratuais
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Contrato -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Contrato</h6>
                        @if ($host->itemContrato && $host->itemContrato->contrato)
                            <p class="fw-semibold text-dark mb-0">
                                {{ $host->itemContrato->contrato->numero ?? '—' }}
                            </p>
                            <small class="text-muted">
                                {{ Str::limit($host->itemContrato->contrato->objeto ?? '', 120) }}
                            </small>
                        @else
                            <p class="text-muted mb-0">—</p>
                        @endif
                    </div>

                    <!-- Item Contratual -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Item do Contrato</h6>
                        @if ($host->itemContrato)
                            <p class="fw-semibold text-dark mb-0">{{ $host->itemContrato->descricao_item }}</p>
                            <small class="text-muted">
                                Unidade: {{ $host->itemContrato->unidade_medida ?? '—' }} |
                                Valor Unitário: R$
                                {{ number_format($host->itemContrato->valor_unitario ?? 0, 2, ',', '.') }}
                            </small>
                        @else
                            <p class="text-muted mb-0">—</p>
                        @endif
                    </div>
                </div>

                @if ($host->itemContrato && $host->itemContrato->contrato)
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Tipo de Item</h6>
                            <p class="fw-semibold text-dark mb-0">{{ $host->itemContrato->tipo_item ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Quantidade Contratada</h6>
                            <p class="fw-semibold text-dark mb-0">{{ $host->itemContrato->quantidade ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Valor Total</h6>
                            <p class="fw-semibold text-dark mb-0">
                                R$ {{ number_format($host->itemContrato->valor_total ?? 0, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- 🔹 Metadados -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 text-secondary fw-semibold">
                    <i class="fas fa-clock me-2 text-primary"></i> Registro e Atualizações
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Criado em:</strong> {{ $host->created_at->format('d/m/Y H:i') }}</p>
                <p class="mb-0">
                    <strong>Última atualização:</strong>
                    {{ optional($host->updated_at)->format('d/m/Y H:i') ?? '—' }}
                </p>
            </div>
        </div>

    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.info('%c[Hosts.Show] Detalhes carregados com sucesso', 'color: green;');
        });
    </script>
@stop
