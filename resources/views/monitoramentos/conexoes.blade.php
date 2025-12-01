@extends('layouts.app')

@section('title', 'Monitoramento de Conexões')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    {{-- RESUMO EM CARDS --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-network-wired fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total de Hosts</h6>
                        <h3 class="mb-0 fw-bold">{{ $resumo['total'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Online</h6>
                        <h3 class="mb-0 fw-bold text-success">{{ $resumo['online'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Offline</h6>
                        <h3 class="mb-0 fw-bold text-danger">{{ $resumo['offline'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICO DE LATÊNCIA --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-secondary fw-semibold">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Histórico de Latência (Host selecionado)
                    </h5>
                    <small class="text-muted" id="graficoHostNome">Selecione um host na tabela abaixo…</small>
                </div>
                <div class="card-body">
                    <canvas id="graficoLatencia" class="canvas-h-260"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- TABELA DE ÚLTIMOS MONITORAMENTOS --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-list text-primary me-2"></i>
                Últimos Monitoramentos
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-monitoramentos" class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Host</th>
                            <th>IP / Alvo</th>
                            <th>Status</th>
                            <th>Latência (ms)</th>
                            <th>Código</th>
                            <th>Última verificação</th>
                            <th>Erro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ultimos as $m)
                            <tr data-host-id="{{ $m->host->id ?? '' }}"
                                data-host-nome="{{ $m->host->nome_conexao ?? 'N/D' }}" class="cursor-pointer">
                                <td>{{ $m->host->nome_conexao ?? 'N/D' }}</td>
                                <td>{{ $m->host->host_alvo ?? $m->host->ip_atingivel ?? 'N/D' }}</td>
                                <td>
                                    @if($m->online)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i> Online
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i> Offline
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $m->latencia ? number_format($m->latencia, 2, ',', '.') : '—' }}</td>
                                <td>{{ $m->status_code ?? '—' }}</td>
                                <td>{{ optional($m->ultima_verificacao)->format('d/m/Y H:i:s') ?? '—' }}</td>
                                <td>
                                    @if($m->erro)
                                        <span class="text-truncate d-inline-block max-w-240" title="{{ $m->erro }}">
                                            {{ Str::limit($m->erro, 60) }}
                                        </span>
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
    </div>

</div>
@endsection

@push('js')
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(function () {
            // DataTables
            $('#tabela-monitoramentos').DataTable({
                pageLength: 25,
                order: [[5, 'desc']],
                language: { url: window.DataTablesLangUrl },
                dom: 't<"bottom"ip>'
            });
        });

        let graficoLatencia = null;

        function inicializarGrafico() {
            const ctx = document.getElementById('graficoLatencia').getContext('2d');
            graficoLatencia = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Latência (ms)',
                        data: [],
                        tension: 0.3,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: { autoSkip: true, maxTicksLimit: 10 }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function carregarGraficoLatencia(hostId, hostNome) {
            if (!hostId) return;

            document.getElementById('graficoHostNome').innerText =
                'Host selecionado: ' + hostNome;

            fetch(`/api/monitoramentos/historico/${hostId}`)
                .then(resp => resp.json())
                .then(dados => {
                    const labels = dados.map(item => item.ultima_verificacao);
                    const latencias = dados.map(item => item.latencia || 0);

                    graficoLatencia.data.labels = labels.reverse();
                    graficoLatencia.data.datasets[0].data = latencias.reverse();
                    graficoLatencia.update();
                })
                .catch(err => {
                    console.error('Erro ao carregar histórico:', err);
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            inicializarGrafico();

            // Clique na linha da tabela carrega o gráfico para aquele host
            document.querySelectorAll('#tabela-monitoramentos tbody tr').forEach(tr => {
                tr.addEventListener('click', function () {
                    const hostId = this.getAttribute('data-host-id');
                    const hostNome = this.getAttribute('data-host-nome');
                    carregarGraficoLatencia(hostId, hostNome);
                });
            });

            // Carrega o primeiro host automaticamente (se existir)
            const primeiraLinha = document.querySelector('#tabela-monitoramentos tbody tr');
            if (primeiraLinha) {
                carregarGraficoLatencia(
                    primeiraLinha.getAttribute('data-host-id'),
                    primeiraLinha.getAttribute('data-host-nome')
                );
            }
        });
    </script>
@endpush
