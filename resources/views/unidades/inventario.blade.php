@extends('layouts.app')

@section('title','Inventário da Unidade')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">{{ $unidade->nome }} ({{ $unidade->tipo }})</h4>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white">Equipamentos</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Hostname</th>
                            <th>Serial</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipamentos as $e)
                            <tr>
                                <td>{{ $e->hostname }}</td>
                                <td>{{ $e->serial_number }}</td>
                                <td>
                                    <form method="POST" action="{{ route('equipamentos.quebra', $e->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="descricao" value="Quebra comunicada pela unidade">
                                        <button class="btn btn-sm btn-warning">Comunicar quebra</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted">Sem equipamentos cadastrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <hr>
            <div class="row g-3">
                <div class="col-md-6">
                    <h6>Ocorrências recentes</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Equipamento</th><th>Tipo</th><th>Status</th><th>Ações</th></tr></thead>
                            <tbody>
                            @foreach(($ocorrencias ?? []) as $o)
                                @php $eq = $equipamentos->firstWhere('id',$o->equipamento_id); @endphp
                                <tr>
                                    <td>{{ $eq?->hostname }} ({{ $eq?->serial_number }})</td>
                                    <td>{{ $o->tipo }}</td>
                                    <td>{{ $o->status }}</td>
                                    <td class="d-flex gap-1">
                                        <form method="POST" action="{{ route('ocorrencias.cit.receber', $o->id) }}">@csrf<button class="btn btn-outline-primary btn-sm">Receber (CIT)</button></form>
                                        <form method="POST" action="{{ route('ocorrencias.cit.avaliar', $o->id) }}" class="d-inline">@csrf<input type="hidden" name="decisao" value="reposicao"><button class="btn btn-outline-warning btn-sm">Sugerir Reposição (CIT)</button></form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Reposições recentes</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Item</th><th>Qtd</th><th>Status</th><th>Ações</th></tr></thead>
                            <tbody>
                            @foreach(($reposicoes ?? []) as $rp)
                                @php $it = $itens->firstWhere('id',$rp->contrato_item_id); @endphp
                                <tr>
                                    <td>{{ $it?->descricao_item }}</td>
                                    <td>{{ $rp->quantidade }}</td>
                                    <td>{{ $rp->status }}</td>
                                    <td class="d-flex gap-1">
                                        <form method="POST" action="{{ route('reposicoes.detec.aprovar', $rp->id) }}">@csrf<button class="btn btn-outline-success btn-sm">Aprovar (DETEC)</button></form>
                                        <form method="POST" action="{{ route('reposicoes.detec.entregar', $rp->id) }}">@csrf<button class="btn btn-outline-info btn-sm">Registrar Entrega</button></form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white">Escolas da Abrangência</div>
        <div class="card-body">
            @if(isset($dre) && $dre)
                <form method="GET" action="{{ route('unidades.inventario', $unidade->id) }}" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Município</label>
                        <select name="municipio" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach($municipios as $m)
                                <option value="{{ $m }}" {{ ($municipio ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="text-muted">DRE: {{ $dre->nome_dre }} — {{ $dre->municipio_sede }}</div>
                    </div>
                </form>
                <div class="mb-3">
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('unidades.especificacoes', [$unidade->id, 'municipio' => $municipio]) }}">Gerar Especificações</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Escola</th>
                                <th>Município</th>
                                <th>Telefone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($escolas as $esc)
                                <tr>
                                    <td>{{ $esc->escola }}</td>
                                    <td>{{ $esc->municipio }}</td>
                                    <td>{{ $esc->telefone }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Nenhuma escola encontrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted">DRE não identificada para esta unidade.</div>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white">Conexões de Internet</div>
        <div class="card-body">
            <div class="table-responsive mb-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Alvo</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hosts as $h)
                            <tr>
                                <td>{{ $h->nome_conexao }}</td>
                                <td>{{ $h->host_alvo }}</td>
                                <td>{{ $h->tipo_monitoramento }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted">Sem conexões cadastradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('unidades.conexoes.store', $unidade->id) }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Nome da conexão</label>
                    <input type="text" name="nome_conexao" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Alvo (IP/URL)</label>
                    <input type="text" name="host_alvo" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de monitoramento</label>
                    <select name="tipo_monitoramento" class="form-select" required>
                        <option value="ping">Ping</option>
                        <option value="porta">Porta</option>
                        <option value="http">HTTP</option>
                        <option value="snmp">SNMP</option>
                        <option value="mikrotik">Mikrotik</option>
                        <option value="speedtest">Speedtest</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Item contratado (opcional)</label>
                    <select name="contrato_item_id" class="form-select">
                        <option value="">—</option>
                        @foreach($itens as $item)
                            <option value="{{ $item->id }}">{{ $item->descricao_item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Adicionar conexão</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white">Incluir Equipamento</div>
        <div class="card-body">
            <form method="POST" action="{{ route('unidades.inventario.store', $unidade->id) }}" class="row g-3">
                @csrf
                <div class="col-md-12">
                    <label class="form-label">Origem</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="origem" value="centralizada" id="origemCentral" checked>
                            <label class="form-check-label" for="origemCentral">Compra centralizada (Contrato)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="origem" value="local" id="origemLocal">
                            <label class="form-check-label" for="origemLocal">Compra da unidade (verba repassada)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de equipamento</label>
                    <select name="tipo_equipamento" class="form-select" id="tipoEquipamento" required>
                        <option value="computador">Computador</option>
                        <option value="switch">Switch</option>
                        <option value="roteador">Roteador</option>
                        <option value="ap">Access Point</option>
                        <option value="impressora">Impressora</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>
                <div class="col-md-6 origem-central">
                    <label class="form-label">Item contratado</label>
                    <select name="contrato_item_id" class="form-select">
                        @foreach($itens as $item)
                            <option value="{{ $item->id }}">{{ $item->descricao_item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hostname</label>
                    <input type="text" name="hostname" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Serial</label>
                    <input type="text" name="serial_number" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sistema Operacional</label>
                    <input type="text" name="sistema_operacional" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">CPU</label>
                    <input type="text" name="cpu_resumida" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">RAM (GB)</label>
                    <input type="number" name="ram_gb" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">IP</label>
                    <input type="text" name="ip_atual" class="form-control">
                </div>
                <div class="col-md-12 origem-local d-none">
                    <hr>
                    <h6>Dados da compra da unidade</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Descrição</label>
                            <input type="text" name="descricao_local" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Documento (NF/Termo)</label>
                            <input type="text" name="documento_local" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Valor (R$)</label>
                            <input type="number" step="0.01" name="valor_local" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data da aquisição</label>
                            <input type="date" name="data_aquisicao" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white">Solicitar reposição</div>
        <div class="card-body">
            <form method="POST" action="{{ route('unidades.reposicoes.solicitar', $unidade->id) }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Equipamento (opcional)</label>
                    <select name="equipamento_id" class="form-select">
                        <option value="">—</option>
                        @foreach($equipamentos as $e)
                            <option value="{{ $e->id }}">{{ $e->hostname }} ({{ $e->serial_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Item contratado</label>
                    <select name="contrato_item_id" class="form-select" required>
                        @foreach($itens as $item)
                            <option value="{{ $item->id }}">{{ $item->descricao_item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantidade</label>
                    <input type="number" min="1" name="quantidade" class="form-control" value="1" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Motivo</label>
                    <textarea name="motivo" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                    <button class="btn btn-success">Solicitar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const origemCentral = document.getElementById('origemCentral');
  const origemLocal = document.getElementById('origemLocal');
  const secLocal = document.querySelector('.origem-local');
  const secCentral = document.querySelector('.origem-central');
  function update() {
    const isLocal = origemLocal.checked;
    secLocal.classList.toggle('d-none', !isLocal);
    secCentral.classList.toggle('d-none', isLocal);
  }
  [origemCentral, origemLocal].forEach(r => r.addEventListener('change', update));
  update();
});
</script>
@endsection
