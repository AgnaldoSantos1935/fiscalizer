@extends('layouts.app')

@section('title', 'Equipamentos')
@section('content_header_title','Equipamentos')
@section('content_header_subtitle','Inventário')

@section('content_body')
<div class="container-fluid">

    <!-- 🔹 CARD DE FILTROS -->
    <div class="card ui-card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header ui-card-header border-0 d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0 fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
            </h4>
        </div>

        <div class="card-body bg-white">
            <form id="formFiltros" class="row g-3 p-3 bg-light rounded-4 shadow-sm" method="GET" action="{{ route('equipamentos.index') }}">

                <div class="col-md-3">
                    <label class="form-label ui-form-label small">Hostname</label>
                    <input id="filtroHostname" type="text" class="form-control form-control-sm"
                           placeholder="Ex: PC-LAB-01">
                </div>

                <div class="col-md-3">
                    <label class="form-label ui-form-label small">Serial Number</label>
                    <input id="filtroSerial" type="text" class="form-control form-control-sm"
                           placeholder="Ex: SN1234567">
                </div>

                <div class="col-md-3">
                    <label class="form-label ui-form-label small">Sistema Operacional</label>
                    <input id="filtroSO" type="text" class="form-control form-control-sm"
                           placeholder="Ex: Windows 10">
                </div>

                <div class="col-md-3">
                    <label class="form-label ui-form-label small">Origem do Inventário</label>
                    <select id="filtroOrigem" class="ui-select form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="manual">Manual</option>
                        <option value="agente">Agente</option>
                        <option value="importacao">Importação</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label ui-form-label small">Tipo</label>
                    <select id="filtroTipo" class="ui-select form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="desktop">Desktop</option>
                        <option value="notebook">Notebook</option>
                        <option value="servidor">Servidor</option>
                        <option value="switch">Switch</option>
                        <option value="roteador">Roteador</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label ui-form-label small">Unidade</label>
                    <select id="filtroUnidade" name="unidade_id" class="ui-select form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach(($unidades ?? []) as $u)
                          <option value="{{ $u->id }}" @selected(request('unidade_id')==$u->id)>{{ $u->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button id="btnAplicarFiltros" type="submit" class="btn ui-btn btn-sm w-100 btn-sep">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <a id="btnLimpar" href="{{ route('equipamentos.index') }}" class="btn btn-sm ui-btn outline w-100 btn-sep">
                        <i class="fas fa-undo me-1"></i> Limpar
                    </a>
                </div>

            </form>
        </div>
    </div>

    <!-- 🔹 LISTAGEM -->
    <div class="card ui-card shadow-sm border-0 rounded-4">
        <div class="card-header ui-card-header border-0 d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0 fw-semibold">
                <i class="fas fa-desktop me-2 text-primary"></i>Equipamentos Cadastrados
            </h4>
        </div>

        <div class="card-body bg-white">

            <!-- 🔹 NAV AÇÕES -->
            <nav class="nav nav-pills flex-column flex-sm-row mb-3">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a id="navDetalhes" href="#" class="nav-link disabled">
                            <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
                        </a>
                    </li>


                </ul>
            </nav>

            <!-- 🔹 TABELA -->
            <table id="tabelaEquipamentos" class="table table-striped table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th style="width: 40px"></th>
                        <th>Hostname</th>
                        <th>Serial</th>
                        <th>SO</th>
                        <th>RAM</th>
                        <th>CPU</th>
                        <th>IP Atual</th>
                        <th>Origem</th>
                        <th>Tipo</th>
                        <th>Último Check-in</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach(($equipamentos ?? []) as $e)
                    <tr>
                      <td class="text-center"><input type="radio" name="equipamentoSelecionado" value="{{ $e->id }}"></td>
                      <td>{{ $e->hostname }}</td>
                      <td>{{ $e->serial_number }}</td>
                      <td>{{ $e->sistema_operacional ?? '—' }}</td>
                      <td>{{ $e->ram_gb ? $e->ram_gb.' GB' : '—' }}</td>
                      <td>{{ $e->cpu_resumida ?? '—' }}</td>
                      <td>{{ $e->ip_atual ?? '—' }}</td>
                      <td>
                        @php
                          $oi = $e->origem_inventario;
                          $map = [
                            'manual' => ['bg-warning text-dark','Manual'],
                            'agente' => ['bg-success','Agente'],
                            'importacao' => ['bg-primary','Importação'],
                          ];
                          [$cls,$nome] = $map[$oi] ?? ['bg-secondary',$oi];
                        @endphp
                        <span class="badge {{ $cls }}">{{ $nome }}</span>
                      </td>
                      <td>
                        @php
                          $tp = $e->tipo;
                          $tmap = [
                            'desktop' => ['bg-primary','Desktop'],
                            'notebook' => ['bg-info text-dark','Notebook'],
                            'servidor' => ['bg-dark','Servidor'],
                            'switch' => ['bg-warning text-dark','Switch'],
                            'roteador' => ['bg-success','Roteador'],
                            'outro' => ['bg-secondary','Outro'],
                          ];
                          [$tcls,$tnome] = $tmap[$tp] ?? ['bg-secondary',$tp];
                        @endphp
                        <span class="badge {{ $tcls }}">{{ $tnome }}</span>
                      </td>
                      <td>{{ $e->ultimo_checkin ? $e->ultimo_checkin->format('d/m/Y H:i') : '—' }}</td>
                    </tr>
                  @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection


@push('js')
<script>
$(function(){
  let equipamentoSelecionado = null;
  $('#tabelaEquipamentos').on('change','input[name="equipamentoSelecionado"]',function(){
    equipamentoSelecionado = $(this).val();
    $('#navDetalhes').removeClass('disabled');
  });
  $('#navDetalhes').on('click',function(e){
    e.preventDefault();
    if (!equipamentoSelecionado) return;
    window.location.href = '{{ url("equipamentos") }}' + '/' + equipamentoSelecionado;
  });
});
</script>
@endpush
