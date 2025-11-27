@extends('layouts.app')

@section('title','Especificações Automatizadas')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ $unidade->nome }} — Especificações</h4>
        <div>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('unidades.especificacoes', [$unidade->id, 'format' => 'json', 'municipio' => $municipio] ) }}">Exportar JSON</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-white">Parâmetros</div>
        <div class="card-body">
            <form method="GET" action="{{ route('unidades.especificacoes', $unidade->id) }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Município</label>
                    <input type="text" class="form-control" name="municipio" value="{{ $municipio }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Computadores base</label>
                    <input type="number" class="form-control" name="baseline_computers" value="{{ $params['baseline_computers'] }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Computadores/AP</label>
                    <input type="number" class="form-control" name="computers_per_ap" value="{{ $params['computers_per_ap'] }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fator de segurança</label>
                    <input type="number" step="0.1" class="form-control" name="safety_factor" value="{{ $params['safety_factor'] }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Metros por ponto</label>
                    <input type="number" class="form-control" name="meters_per_drop" value="{{ $params['meters_per_drop'] }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Aplicar</button>
                </div>
            </form>
            <hr>
            <form method="GET" action="{{ route('unidades.especificacoes', $unidade->id) }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Consulta semântica (RAG)</label>
                    <input type="text" class="form-control" name="rag_q" value="{{ request('rag_q') }}" placeholder="Ex.: justificar uso de Cat6">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fonte</label>
                    <input type="text" class="form-control" name="rag_fonte" value="{{ request('rag_fonte') }}" placeholder="Ex.: NBR 14565">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tags</label>
                    <input type="text" class="form-control" name="rag_tags[]" value="{{ is_array(request('rag_tags')) ? (request('rag_tags')[0] ?? '') : '' }}" placeholder="Ex.: cabeamento">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-primary w-100">Buscar</button>
                </div>
            </form>
            <form method="POST" action="{{ route('unidades.normas.upload', $unidade->id) }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Upload de Norma (PDF)</label>
                    <input type="file" class="form-control" name="arquivo" accept="application/pdf">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fonte</label>
                    <input type="text" class="form-control" name="fonte" placeholder="Ex.: NBR 14565">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Idioma</label>
                    <input type="text" class="form-control" name="idioma" value="pt-BR">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tags</label>
                    <input type="text" class="form-control" name="tags[]" placeholder="cabeamento">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-success w-100">Indexar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Computadores</div><div class="fs-4">{{ $spec['totais']['computadores'] }}</div></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">APs</div><div class="fs-4">{{ $spec['totais']['aps'] }}</div></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Portas de Switch</div><div class="fs-4">{{ $spec['totais']['switch_ports'] }}</div></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Cabo (m)</div><div class="fs-4">{{ $spec['totais']['cabo_metros'] }}</div></div></div></div>
    </div>

    <div class="card">
        <div class="card-header bg-white">Dimensionamento por Escola</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr>
                        <th>Escola</th>
                        <th>Município</th>
                        <th>Computadores</th>
                        <th>APs</th>
                        <th>Portas</th>
                        <th>Pts. Cabo</th>
                        <th>Cabos (m)</th>
                    </tr></thead>
                    <tbody>
                        @foreach($spec['items'] as $i)
                            <tr>
                                <td>{{ $i['escola'] }}</td>
                                <td>{{ $i['municipio'] }}</td>
                                <td>{{ $i['computadores'] }}</td>
                                <td>{{ $i['aps'] }}</td>
                                <td>{{ $i['switch_ports'] }}</td>
                                <td>{{ $i['cabo_drops'] }}</td>
                                <td>{{ $i['cabo_metros'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <h6>Recomendações de especificações</h6>
                <ul>
                    <li>Switch: {{ $spec['recomendacoes']['switch_modelo'] }}</li>
                    <li>Access Point: {{ $spec['recomendacoes']['ap_modelo'] }}</li>
                    <li>Cabeamento: {{ $spec['recomendacoes']['cabo'] }}</li>
                </ul>
                <h6>Normas aplicáveis</h6>
                <ul>
                    @foreach($spec['normas'] as $n)
                        <li>{{ $n }}</li>
                    @endforeach
                </ul>
                @if(isset($spec['motor']['recomendacoes']))
                <h6>Recomendações justificadas e normatizadas</h6>
                <div class="row g-3">
                    @foreach($spec['motor']['recomendacoes'] as $key => $rec)
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="text-muted">{{ strtoupper($key) }}</div>
                                    <div>{{ $rec['descricao'] }}</div>
                                    <div class="mt-2"><strong>Justificativa:</strong> {{ $rec['justificativa'] }}</div>
                                    <div class="mt-2"><strong>Normas:</strong>
                                        <ul>
                                            @foreach(($rec['normas'] ?? []) as $nn)
                                                <li>{{ $nn }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @if(isset($spec['fundamentacao']))
    <div class="card mt-3">
        <div class="card-header bg-white">Fundamentação normativa (RAG)</div>
        <div class="card-body">
            @foreach($spec['fundamentacao'] as $key => $f)
                <div class="mb-3">
                    <div class="fw-semibold">{{ $f['afirmacao'] }}</div>
                    <ul class="mt-2">
                        @foreach(($f['trechos'] ?? []) as $t)
                            <li>
                                <span>“{{ $t['texto'] }}”</span>
                                <span class="text-muted"> — {{ $t['fonte'] }}{{ $t['referencia'] ? ', ' . $t['referencia'] : '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-2">{{ $f['conclusao'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @if(isset($spec['rag_busca']))
    <div class="card mt-3">
        <div class="card-header bg-white">Resultados da busca semântica</div>
        <div class="card-body">
            <div class="mb-2">Consulta: <strong>{{ $spec['rag_busca']['consulta'] }}</strong></div>
            <ul>
                @foreach(($spec['rag_busca']['resultados'] ?? []) as $t)
                    <li>
                        <span>“{{ $t['texto'] }}”</span>
                        <span class="text-muted"> — {{ $t['fonte'] }}{{ $t['referencia'] ? ', ' . $t['referencia'] : '' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    @if(isset($spec['dashboard']['municipios']))
    <div class="card mt-3">
        <div class="card-header bg-white">Planejamento de Infraestrutura — Distribuição por município</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Município</th><th>Computadores</th><th>APs</th><th>Portas</th><th>Cabo (m)</th><th>%</th></tr></thead>
                    <tbody>
                        @foreach($spec['dashboard']['municipios'] as $mun => $vals)
                        <tr>
                            <td>{{ $mun }}</td>
                            <td>{{ $vals['computadores'] }}</td>
                            <td>{{ $vals['aps'] }}</td>
                            <td>{{ $vals['switch_ports'] }}</td>
                            <td>{{ $vals['cabo_metros'] }}</td>
                            <td style="width:200px">
                                <div class="bg-light" style="height:18px; border-radius:10px; overflow:hidden;">
                                    <div class="bg-primary" style="width: {{ $vals['pct'] }}%; height:18px"></div>
                                </div>
                                <small class="text-muted ms-1">{{ $vals['pct'] }}%</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
