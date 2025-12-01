@extends('layouts.app')

@section('title', 'Detalhes do Equipamento')

@section('content_body')
<div class="container-fluid">

    {{-- Breadcrumb simples (ajuste conforme seu layout) --}}
    <div class="mb-3">
        <a href="{{ route('equipamentos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar para a lista
        </a>
    </div>

    <div class="row g-3">

        <!-- 🔹 Card Identificação / Inventário -->
        <div class="col-lg-4">
            <div class="card ui-card shadow-sm border-0 rounded-4">
                <div class="card-header ui-card-header border-0">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-id-card me-2 text-primary"></i>Identificação
                    </h5>
                </div>
                <div class="card-body bg-white small">
                    <dl class="row mb-0">
                        <dt class="col-4">Hostname</dt>
                        <dd class="col-8">{{ $equipamento->hostname ?? '—' }}</dd>

                        <dt class="col-4">Serial</dt>
                        <dd class="col-8">{{ $equipamento->serial_number ?? '—' }}</dd>

                        <dt class="col-4">Origem</dt>
                        <dd class="col-8">
                            @php
                                $mapOrigem = [
                                    'manual'     => ['badge bg-warning text-dark', 'Manual'],
                                    'agente'     => ['badge bg-success', 'Agente'],
                                    'importacao' => ['badge bg-primary', 'Importação'],
                                ];
                                [$cls, $nome] = $mapOrigem[$equipamento->origem_inventario] ?? ['badge bg-secondary', $equipamento->origem_inventario ?? '—'];
                            @endphp
                            <span class="{{ $cls }}">{{ $nome }}</span>
                        </dd>

                        <dt class="col-4">Tipo</dt>
                        <dd class="col-8">
                            @php
                                $mapTipo = [
                                    'desktop'  => ['badge bg-primary', 'Desktop'],
                                    'notebook' => ['badge bg-info text-dark', 'Notebook'],
                                    'servidor' => ['badge bg-dark', 'Servidor'],
                                    'switch'   => ['badge bg-warning text-dark', 'Switch'],
                                    'roteador' => ['badge bg-success', 'Roteador'],
                                    'outro'    => ['badge bg-secondary', 'Outro'],
                                ];
                                [$clsTipo, $nomeTipo] = $mapTipo[$equipamento->tipo] ?? ['badge bg-secondary', $equipamento->tipo ?? '—'];
                            @endphp
                            <span class="{{ $clsTipo }}">{{ $nomeTipo }}</span>
                        </dd>

                        <dt class="col-4">Unidade</dt>
                        <dd class="col-8">
                            {{ optional($equipamento->unidade)->nome ?? '—' }}
                        </dd>

                        <dt class="col-4">Último check-in</dt>
                        <dd class="col-8">
                            @if($equipamento->ultimo_checkin)
                                {{ $equipamento->ultimo_checkin->format('d/m/Y H:i') }}
                            @else
                                —
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- 🔹 Card Diagnóstico -->
            <div class="card ui-card shadow-sm border-0 rounded-4 mt-3">
                <div class="card-header ui-card-header border-0">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-stethoscope me-2 text-primary"></i>Diagnóstico
                    </h5>
                </div>
                <div class="card-body bg-white small">
                    <p class="mb-1">
                        <strong>Status do check-in:</strong>
                        @switch($diagnostico['status_checkin'])
                            @case('online_recente')
                                <span class="badge bg-success">Online recente</span>
                                @break
                            @case('semana_passada')
                                <span class="badge bg-info text-dark">Atualizado há poucos dias</span>
                                @break
                            @case('desatualizado')
                                <span class="badge bg-warning text-dark">Sem check-in recente</span>
                                @break
                            @case('nunca_reportou')
                                <span class="badge bg-secondary">Nunca reportou</span>
                                @break
                            @default
                                <span class="badge bg-secondary">—</span>
                        @endswitch
                    </p>

                    <p class="mb-0">
                        <strong>Obsolescência (heurística):</strong>
                        @if($diagnostico['eh_obsoleto'])
                            <span class="badge bg-danger">Potencialmente obsoleto</span>
                        @else
                            <span class="badge bg-success">Dentro do aceitável</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- 🔹 Card Hardware / Sistema -->
        <div class="col-lg-4">
            <div class="card ui-card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header ui-card-header border-0">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-microchip me-2 text-primary"></i>Hardware & Sistema
                    </h5>
                </div>
                <div class="card-body bg-white small">
                    <dl class="row mb-0">
                        <dt class="col-4">SO</dt>
                        <dd class="col-8">{{ $equipamento->sistema_operacional ?? '—' }}</dd>

                        <dt class="col-4">RAM</dt>
                        <dd class="col-8">
                            {{ $equipamento->ram_gb ? $equipamento->ram_gb . ' GB' : '—' }}
                        </dd>

                        <dt class="col-4">CPU</dt>
                        <dd class="col-8">{{ $equipamento->cpu_resumida ?? '—' }}</dd>

                        <dt class="col-4">Discos</dt>
                        <dd class="col-8">{{ $equipamento->discos ?? '—' }}</dd>

                        <dt class="col-12">Especificações detalhadas</dt>
                        <dd class="col-12">
                            @if($equipamento->especificacoes)
                                <pre class="small bg-light rounded p-2 mb-0">{{ $equipamento->especificacoes }}</pre>
                            @else
                                <span class="text-muted">Nenhuma especificação detalhada informada.</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- 🔹 Card Rede + Histórico -->
        <div class="col-lg-4">
            <div class="card ui-card shadow-sm border-0 rounded-4 mb-3">
                <div class="card-header ui-card-header border-0">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-network-wired me-2 text-primary"></i>Rede
                    </h5>
                </div>
                <div class="card-body bg-white small">
                    <dl class="row mb-0">
                        <dt class="col-4">IP atual</dt>
                        <dd class="col-8">{{ $equipamento->ip_atual ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card ui-card shadow-sm border-0 rounded-4">
                <div class="card-header ui-card-header border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-history me-2 text-primary"></i>Histórico
                    </h5>
                    {{-- Aqui futuramente um botão "Ver tudo" ou "Exportar" --}}
                </div>
                <div class="card-body bg-white small">
                    @if($historicos->isEmpty())
                        <p class="text-muted mb-0">
                            Nenhum histórico cadastrado ainda.
                            <br>
                            <small>Integre com a tabela de manutenções, OS ou logs do agente.</small>
                        </p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Tipo</th>
                                        <th>Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historicos as $h)
                                        <tr>
                                            <td>{{ $h->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $h->tipo ?? '—' }}</td>
                                            <td>{{ $h->descricao ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
