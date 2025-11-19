@extends('layouts.app')
@section('title','Editar Ata')
@section('content')
@include('layouts.components.breadcrumbs')
@section('breadcrumb')
  @include('layouts.components.breadcrumbs', [
    'trail' => [
      ['label' => 'Atas', 'icon' => 'fas fa-file-alt', 'url' => route('atas.index')],
      ['label' => 'Editar Ata ' . ($ata->numero ?? '')]
    ]
  ])
@endsection
<div class="container-fluid">
  <h3 class="mb-3">Editar Ata {{ $ata->numero }}</h3>
  <form method="POST" action="{{ route('atas.update',$ata->id) }}" class="card p-3">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-3"><label class="form-label">Processo</label><input type="text" name="processo" class="form-control" value="{{ old('processo',$ata->processo) }}"></div>
      <div class="col-md-6"><label class="form-label">Objeto</label><input type="text" name="objeto" class="form-control" value="{{ old('objeto',$ata->objeto) }}" required></div>
      <div class="col-md-6"><label class="form-label">Órgão Gerenciador</label>
        <select name="orgao_gerenciador_id" class="form-select" required>
          @foreach($empresas as $e)<option value="{{ $e->id }}" {{ $ata->orgao_gerenciador_id==$e->id?'selected':'' }}>{{ $e->razao_social }}</option>@endforeach
        </select>
      </div>
      <div class="col-md-6"><label class="form-label">Fornecedor</label>
        <select name="fornecedor_id" class="form-select" required>
          @foreach($empresas as $e)<option value="{{ $e->id }}" {{ $ata->fornecedor_id==$e->id?'selected':'' }}>{{ $e->razao_social }}</option>@endforeach
        </select>
      </div>
      <div class="col-md-3"><label class="form-label">Publicação</label><input type="date" name="data_publicacao" class="form-control" value="{{ optional($ata->data_publicacao)->format('Y-m-d') }}"></div>
      <div class="col-md-3"><label class="form-label">Início Vigência</label><input type="date" name="vigencia_inicio" class="form-control" value="{{ optional($ata->vigencia_inicio)->format('Y-m-d') }}"></div>
      <div class="col-md-3"><label class="form-label">Fim Vigência</label><input type="date" name="vigencia_fim" class="form-control" value="{{ optional($ata->vigencia_fim)->format('Y-m-d') }}"></div>
      <div class="col-md-3"><label class="form-label">Prorrogação (meses)</label><input type="number" name="prorroga_total_meses" class="form-control" min="0" value="{{ old('prorroga_total_meses',$ata->prorroga_total_meses) }}"></div>
      <div class="col-md-3"><label class="form-label">Saldo Global</label><input type="text" name="saldo_global" class="form-control money-br-input" value="{{ old('saldo_global',$ata->saldo_global) }}"></div>
      <div class="col-md-3"><label class="form-label">Situação</label>
        <select name="situacao" class="form-select">
          @foreach(['vigente','expirada','suspensa','revogada'] as $s)<option value="{{ $s }}" {{ $ata->situacao===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
        </select>
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-primary">Salvar</button>
      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>

  <div class="mt-4 card p-3">
    <h5>Adesões</h5>
    <div class="mb-2">Saldo disponível: <strong>R$ {{ number_format($ata->saldo_disponivel ?? 0, 2, ',', '.') }}</strong></div>
    <form method="POST" action="{{ route('atas.adesoes.store',$ata->id) }}" class="row g-3">
      @csrf
      <div class="col-md-6"><label class="form-label">Órgão Adquirente</label>
        <select name="orgao_adquirente_id" class="form-select" required>
          <option value="">Selecione...</option>
          @foreach($empresas as $e)<option value="{{ $e->id }}">{{ $e->razao_social }}</option>@endforeach
        </select>
      </div>
      <div class="col-md-6"><label class="form-label">Justificativa</label><input type="text" name="justificativa" class="form-control" required></div>
      <div class="col-md-3"><label class="form-label">Valor Estimado (opcional)</label><input type="text" name="valor_estimado" class="form-control money-br-input" placeholder="Somado dos itens se não preencher"></div>
      <div class="col-12">
        <div class="card mt-2">
          <div class="card-header">Selecionar Itens da Ata</div>
          <div class="card-body p-0">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Descrição</th>
                  <th class="text-end">Saldo disponível</th>
                  <th class="text-end">Preço unitário</th>
                  <th style="width:160px">Quantidade</th>
                  <th class="text-end" style="width:160px">Valor unitário (opcional)</th>
                </tr>
              </thead>
              <tbody>
                @foreach($ata->itens as $i)
                  <tr>
                    <td>{{ $i->descricao }}</td>
                    <td class="text-end">{{ number_format($i->saldo_disponivel ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($i->preco_unitario ?? 0, 4, ',', '.') }}</td>
                    <td>
                      <input type="hidden" name="itens[{{ $i->id }}][id]" value="{{ $i->id }}" />
                      <input type="number" step="0.01" min="0" max="{{ $i->saldo_disponivel ?? 0 }}" name="itens[{{ $i->id }}][quantidade]" class="form-control form-control-sm" placeholder="Qtd" />
                    </td>
                    <td>
<input type="text" name="itens[{{ $i->id }}][valor_unitario]" class="form-control form-control-sm money-br-input" data-decimals="4" placeholder="{{ number_format($i->preco_unitario ?? 0, 4, ',', '.') }}" />
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-12"><button class="btn btn-outline-primary">Registrar Adesão</button></div>
    </form>
    <table class="table table-sm mt-3">
      <thead><tr><th>Órgão</th><th>Status</th><th>Solicitação</th><th>Valor</th><th>Documento</th><th>Ações</th></tr></thead>
      <tbody>
        @foreach($ata->adesoes as $ad)
          <tr>
            <td>{{ $ad->orgaoAdquirente->razao_social ?? '—' }}</td>
            <td>{{ $ad->status }}</td>
            <td>{{ optional($ad->data_solicitacao)->format('d/m/Y') }}</td>
            <td>R$ {{ number_format($ad->valor_estimado ?? 0, 2, ',', '.') }}</td>
            <td>
              @if($ad->documento_pdf_path)
                <a href="{{ asset('storage/'.$ad->documento_pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Baixar</a>
              @else
                —
              @endif
            </td>
            <td class="d-flex gap-1">
              <form method="POST" action="{{ route('adesoes.gerar_pdf',$ad->id) }}">
                @csrf
                <button class="btn btn-sm btn-secondary">Gerar PDF</button>
              </form>
              <form method="POST" action="{{ route('adesoes.status',$ad->id) }}" class="ms-1">
                @csrf
                <input type="hidden" name="status" value="autorizada" />
                <button class="btn btn-sm btn-success">Autorizar</button>
              </form>
              <form method="POST" action="{{ route('adesoes.status',$ad->id) }}" class="ms-1">
                @csrf
                <input type="hidden" name="status" value="negada" />
                <button class="btn btn-sm btn-outline-danger">Negar</button>
              </form>
              <form method="POST" action="{{ route('adesoes.status',$ad->id) }}" class="ms-1">
                @csrf
                <input type="hidden" name="status" value="cancelada" />
                <button class="btn btn-sm btn-outline-warning">Cancelar</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
