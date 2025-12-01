@extends('layouts.app')

@section('title', "Análise do Documento Técnico – Demanda #{$demanda->id}")

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="fas fa-file-alt text-primary"></i>
            Documento Técnico – Demanda #{{ $demanda->id }}
        </h3>

        <div>
            @php
                $__path = $doc->arquivo_path ?? '';
                $__ext = strtolower(pathinfo($__path, PATHINFO_EXTENSION));
                $__icon = 'fa-file'; $__color = '';
                switch ($__ext) {
                    case 'pdf': $__icon = 'fa-file-pdf'; $__color = 'text-danger'; break;
                    case 'doc': case 'docx': $__icon = 'fa-file-word'; $__color = 'text-primary'; break;
                    case 'xls': case 'xlsx': $__icon = 'fa-file-excel'; $__color = 'text-success'; break;
                    case 'ppt': case 'pptx': $__icon = 'fa-file-powerpoint'; $__color = 'text-danger'; break;
                    case 'zip': case 'rar': $__icon = 'fa-file-archive'; $__color = 'text-warning'; break;
                    case 'jpg': case 'jpeg': case 'png': case 'gif': case 'webp': $__icon = 'fa-file-image'; $__color = 'text-info'; break;
                    case 'txt': $__icon = 'fa-file-alt'; break;
                }
            @endphp
            <a href="{{ route('arquivos.visualizar', ['path' => $doc->arquivo_path, 'return_to' => url()->current()]) }}" class="btn btn-outline-primary">
                <i class="fas fa-eye"></i> Abrir
            </a>

            <a href="{{ route('demanda.reprocessar_ia', $demanda->id) }}" class="btn btn-outline-warning">
                <i class="fas fa-sync"></i> Reprocessar IA
            </a>

            @if($doc->status_validacao === 'valido')
            <a href="{{ route('os.emitir', $demanda->id) }}" class="btn btn-success">
                <i class="fas fa-check"></i> Emitir Ordem de Serviço
            </a>
            @endif
        </div>
    </div>

    <div class="row">

        {{-- RESUMO --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <strong>Resumo da Demanda</strong>
                </div>
                <div class="card-body">
                    <p><strong>Título:</strong> {{ $demanda->titulo }}</p>
                    <p><strong>Tipo:</strong> {{ $demanda->tipo_manutencao }}</p>
                    <p><strong>Sistema:</strong> {{ optional($demanda->sistema)->nome ?? '—' }}</p>
                    <p><strong>Módulo:</strong> {{ optional($demanda->modulo)->nome ?? '—' }}</p>
                    <p><strong>Status:</strong> {{ $demanda->status }}</p>
                    <p><strong>Recebido em:</strong> {{ $doc->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- RESULTADO DA IA --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-secondary text-white">
                    <strong>Resultado da IA</strong>
                </div>
                <div class="card-body">

                    <div class="row text-center">
                        <div class="col-md-3">
                            <h4 class="text-primary">{{ $doc->pf_estimado ?? '—' }}</h4>
                            <p class="text-muted small">PF Estimado</p>
                        </div>

                        <div class="col-md-3">
                            <h4 class="text-primary">{{ $doc->ust_estimado ?? '—' }}</h4>
                            <p class="text-muted small">UST Estimado</p>
                        </div>

                        <div class="col-md-3">
                            <h4 class="text-primary">
                                {{ $resumo['produtividade']['horas_por_pf'] ?? '—' }}
                            </h4>
                            <p class="text-muted small">Horas / PF</p>
                        </div>

                        <div class="col-md-3">
                            <h4 class="text-primary">{{ $doc->status_validacao }}</h4>
                            <p class="text-muted small">Validação</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- INCONSISTÊNCIAS --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-danger text-white">
            <strong>Inconsistências Encontradas</strong>
        </div>
        <div class="card-body">
            @if(!empty($doc->inconsistencias_json))
                <ul class="list-group">
                    @foreach(json_decode($doc->inconsistencias_json, true) as $inc)
                        <li class="list-group-item">
                            <i class="fas fa-exclamation-circle text-danger me-2"></i>
                            {{ $inc }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-success fw-bold">Nenhuma inconsistência identificada.</p>
            @endif
        </div>
    </div>

    {{-- REQUISITOS --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-info text-white">
            <strong>Requisitos Extraídos</strong>
        </div>
        <div class="card-body p-0">
            @php
                $reqs = json_decode($doc->requisitos_json, true) ?? [];
            @endphp

            @if(count($reqs))
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>PF</th>
                        <th>UST</th>
                        <th>Complexidade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reqs as $r)
                    <tr>
                        <td>{{ $r['codigo'] ?? '—' }}</td>
                        <td>{{ $r['titulo'] ?? '—' }}</td>
                        <td>{{ $r['pontos_de_funcao'] ?? '—' }}</td>
                        <td>{{ $r['ust'] ?? '—' }}</td>
                        <td>{{ $r['complexidade'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="p-3">Nenhum requisito estruturado encontrado.</p>
            @endif
        </div>
    </div>

    {{-- CRONOGRAMA --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white">
            <strong>Cronograma</strong>
        </div>
        <div class="card-body">
            @php
                $cron = json_decode($doc->cronograma_json, true) ?? [];
            @endphp

            @if(count($cron))
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Marco</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Duração</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cron as $c)
                        <tr>
                            <td>{{ $c['marco'] }}</td>
                            <td>{{ $c['inicio'] }}</td>
                            <td>{{ $c['fim'] }}</td>
                            <td>{{ $c['duracao_dias'] }} dias</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Nenhum cronograma estruturado no documento.</p>
            @endif
        </div>
    </div>

</div>
@endsection
