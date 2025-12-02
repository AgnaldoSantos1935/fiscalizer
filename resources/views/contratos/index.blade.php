@extends('layouts.app')
@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Contratos')
@section('content_header_title','Contratos')
@section('content_header_subtitle','Listagem')

@section('content_body')
<div class="container-fluid">
  <!-- 🔹 Card de Filtros -->
    <div class="card ui-card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header ui-card-header border-0 d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0 fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
            </h4>
        </div>

        <div class="card-body bg-white">

<form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3" method="GET" action="{{ route('contratos.index') }}">
  <div class="col-12 col-md-3">
    <label for="filtroNumero" class="form-label ui-form-label small">Número</label>
    <input type="text" id="filtroNumero" name="numero"
           value="{{ request('numero') }}" class="form-control form-control-sm" type="text"
           placeholder="Ex: 065/2025">
  </div>

  <div class="col-12 col-md-4">
    <label for="filtroEmpresa" class="form-label ui-form-label small">Empresa</label>
    <input type="text" id="filtroEmpresa" name="empresa"
           value="{{ request('empresa') }}" class="form-control form-control-sm" type="text"
           placeholder="Digite parte do nome">
  </div>

  <div class="col-12 col-md-3">
    <label for="filtroSituacao" class="form-label ui-form-label small">Situação</label>
    <select id="filtroSituacao" name="situacao" class="ui-select">
      <option value="">Todas</option>
      @foreach((isset($situacoes) ? $situacoes : []) as $s)
        <option value="{{ $s->slug }}" @selected(request('situacao')===$s->slug)>{{ $s->nome }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-2 d-flex justify-content-end gap-2">
    <button type="submit" id="btnAplicarFiltros" class="btn btn-primary btn-icon btn-sm" aria-label="Filtrar" title="Filtrar">
      <i class="fas fa-filter"></i>
    </button>
    <a href="{{ route('contratos.index') }}" id="btnLimpar" class="btn btn-warning text-dark btn-icon btn-sm" aria-label="Limpar" title="Limpar">
      <i class="fas fa-undo"></i>
    </a>
  </div>
</form>

        </div>
    </div>

    <!-- 🔹 Card Principal -->
    <div class="card ui-card shadow-sm border-0 rounded-4">
        <div class="card-header ui-card-header border-0 d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0 fw-semibold">
                <i class="fas fa-file-contract me-2 text-primary"></i>Contratos Cadastrados
            </h4>
        </div>

        <div class="card-body bg-white">
              <!-- 🔹 Navbar de ações -->
             <nav class="nav nav-pills flex-column flex-sm-row">

    <ul class="nav nav-pills">
      <li class="nav-item">
        <a id="navDetalhes" class="nav-link disabled" aria-current="page" href="#">
          <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
        </a>
      </li>

    </ul>

</nav>
<br>
<!--Legendas de situação dos contratos-->
<div id="legendaSituacoes" class="mb-3 d-flex flex-wrap align-items-center gap-2 small text-secondary">
  <i class="fas fa-info-circle me-2 text-primary"></i>
  <span> Legenda de Situações: </span>
  <div id="listaLegendas" class="d-flex flex-wrap gap-2 ms-2">
    @foreach(($situacoes ?? []) as $s)
      @php
        $map = array(
          'azul' => 'primary',
          'verde' => 'success',
          'amarelo' => 'warning',
          'vermelho' => 'danger',
          'cinza' => 'secondary',
          'preto' => 'dark',
          'branco' => 'light',
          'ciano' => 'info',
          'roxo' => 'secondary',
          'laranja' => 'warning'
        );
        $slugColor = strtolower(preg_replace('/[^a-z]/','', Str::ascii(isset($s->cor) ? $s->cor : '')));
        $clsBase = isset($map[$slugColor]) ? $map[$slugColor] : 'secondary';
        $needsDark = in_array($clsBase, array('warning','light'));
        $cls = 'badge bg-'.$clsBase.($needsDark ? ' text-dark' : '');
        $descricao = isset($s->descricao) ? $s->descricao : 'Sem descrição.';
      @endphp
      <span class="{{ $cls }} px-3 py-2 shadow-sm d-flex align-items-center gap-1" title="{{ $descricao }}">
        <i class="fas fa-tag"></i> {{ $s->nome }}
      </span>
    @endforeach
  </div>
</div>
<!---->
            <!-- 🔹 Tabela -->
      <table id="tabelaContratos" class="table table-striped table-hover align-middle w-100">
        <thead>
          <tr>
            <th style="width: 40px"></th>
            <th>Número</th>
            <th>Objeto</th>
            <th>Empresa</th>
            <th class="text-end">Valor Global</th>
            <th>Data Início</th>
            <th>Situação</th>
          </tr>
        </thead>
        <tbody>
          @foreach((isset($contratos) ? $contratos : []) as $c)
            <tr>
              <td class="text-center">
                <input type="radio" name="contratoSelecionado" value="{{ $c->id }}">
              </td>
              <td>{{ $c->numero }}</td>
              <td>{{ Str::limit($c->objeto, 80) }}</td>
              <td>{{ optional($c->contratada)->razao_social ?: '—' }}</td>
              <td class="text-end">R$ {{ number_format((float) (isset($c->valor_global) ? $c->valor_global : 0), 2, ',', '.') }}</td>
              <td>{{ optional($c->data_inicio)->format('d/m/Y') ?: '—' }}</td>
              <td>
                @php
                  $s = $c->situacaoContrato;
                  $map = array(
                    'azul' => 'primary',
                    'verde' => 'success',
                    'amarelo' => 'warning',
                    'vermelho' => 'danger',
                    'cinza' => 'secondary',
                    'preto' => 'dark',
                    'branco' => 'light',
                    'ciano' => 'info',
                    'roxo' => 'secondary',
                    'laranja' => 'warning'
                  );
                  $slugColor = strtolower(preg_replace('/[^a-z]/','', Str::ascii(optional($s)->cor ?: '')));
                  $clsBase = isset($map[$slugColor]) ? $map[$slugColor] : 'secondary';
                  $needsDark = in_array($clsBase, array('warning','light'));
                  $cls = 'badge bg-'.$clsBase.($needsDark ? ' text-dark' : '');
                @endphp
                <span class="{{ $cls }}">{{ optional($s)->nome ?: '—' }}</span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
        </div>
    </div>
  </div>


<!-- Modal de Itens Contratados -->
<div class="modal fade" id="modalItensContrato" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-boxes"></i> Itens Contratados</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped dt-skip" id="tabelaItensContrato">
          <thead>
            <tr>
              <th>Descrição</th>
              <th>Unidade</th>
              <th>Quantidade</th>
              <th>Valor Unitário</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody id="listaItensContrato"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Detalhes do Contrato -->
<div class="modal fade" id="modalDetalhesContrato" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title"><i class="fas fa-file-contract"></i> Detalhes do Contrato</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><div><strong>Número</strong></div><div id="detNumero">—</div></div>
          <div class="col-md-6"><div><strong>Situação</strong></div><div id="detSituacao">—</div></div>
          <div class="col-md-12"><div><strong>Objeto</strong></div><div id="detObjeto">—</div></div>
          <div class="col-md-6"><div><strong>Contratada</strong></div><div id="detEmpresa">—</div></div>
          <div class="col-md-6"><div><strong>CNPJ</strong></div><div id="detCnpj">—</div></div>
          <div class="col-md-4"><div><strong>Valor Global</strong></div><div id="detValorGlobal">—</div></div>
          <div class="col-md-4"><div><strong>Data Início</strong></div><div id="detDataInicio">—</div></div>
          <div class="col-md-4"><div><strong>Data Fim</strong></div><div id="detDataFim">—</div></div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('css')

@endpush

@push('js')
<script>
$(function(){
  let contratoSelecionado = null;
  $('#tabelaContratos').on('change', 'input[name="contratoSelecionado"]', function(){
    contratoSelecionado = $(this).val();
    $('#navDetalhes').removeClass('disabled');
  });
  $('#navDetalhes').on('click', function(e){
    e.preventDefault();
    if (!contratoSelecionado) return;
    window.location.href = '{{ route('contratos.index') }}' + '/' + contratoSelecionado;
  });
});
</script>
@endpush
