@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp

@section('title', 'Detalhes do Host')

@section('content')
<div class="container-fluid">

    <!-- Card principal -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-server me-2 text-primary"></i> Detalhes do Host
            </h4>
        </div>

        <div class="card-body bg-white">

            <div class="row g-3">

                <div class="col-md-4">
                    <strong>Nome:</strong><br>
                    {{ $host->nome_conexao }}
                </div>

                <div class="col-md-4">
                    <strong>Provedor:</strong><br>
                    {{ $host->provedor }}
                </div>

                <div class="col-md-4">
                    <strong>Tecnologia:</strong><br>
                    {{ $host->tecnologia }}
                </div>

                <div class="col-md-4">
                    <strong>Tipo Monitoramento:</strong><br>
                    <span class="badge bg-primary">{{ strtoupper($host->tipo_monitoramento) }}</span>
                </div>

                <div class="col-md-4">
                    <strong>Host Alvo:</strong><br>
                    {{ $host->host_alvo }}
                </div>

                <div class="col-md-4">
                    <strong>Porta:</strong><br>
                    {{ $host->porta ?? '—' }}
                </div>

                <div class="col-md-4">
                    <strong>Status:</strong><br>
                    @if($host->status)
                        <span class="badge bg-success">Ativo</span>
                    @else
                        <span class="badge bg-danger">Inativo</span>
                    @endif
                </div>

            </div>

            <hr>

            <h5 class="mt-4 mb-3 text-secondary">
                <i class="fas fa-history me-2 text-primary"></i> Histórico Recente
            </h5>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Latência</th>
                        <th>Código</th>
                        <th>Erro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($host->monitoramentos()->orderBy('ultima_verificacao','DESC')->limit(20)->get() as $m)
                        <tr>
                            <td>{{ optional($m->ultima_verificacao)->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if($m->online)
                                    <span class="badge bg-success">Online</span>
                                @else
                                    <span class="badge bg-danger">Offline</span>
                                @endif
                            </td>
                            <td>{{ $m->latencia ?? '—' }}</td>
                            <td>{{ $m->status_code ?? '—' }}</td>
                            <td>{{ Str::limit($m->erro ?? '—', 40) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>

</div>
@endsection
