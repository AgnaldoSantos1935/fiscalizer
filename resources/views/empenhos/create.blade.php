@extends('layouts.app')
@section('title', 'Cadastrar Nota de Empenho')

@section('content_body')
<div class="container-fluid">
  @section('breadcrumb')
    @include('layouts.components.breadcrumbs', [
      'trail' => array_filter([
        ['label' => 'Contratos', 'icon' => 'fas fa-file-contract', 'url' => route('contratos.index')],
        isset($preContrato) && $preContrato ? ['label' => 'Contrato ' . ($preContrato->numero ?? ''), 'url' => route('contratos.show', $preContrato->id)] : null,
        ['label' => 'Cadastrar Nota de Empenho']
      ])
    ])
  @endsection
  <div class="card rounded-4 shadow-sm">
    <div class="card-header bg-white"><h4 class="mb-0">Cadastrar Nota de Empenho</h4></div>
    <div class="card-body">
      @if(isset($preContrato) && $preContrato)
      <div class="card mb-3 contrato-card">
        <div class="card-header"><strong>Dados do Contrato</strong></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <dl class="row mb-0">
                <dt class="col-sm-4">Número</dt>
                <dd class="col-sm-8">{{ $preContrato->numero ?? '—' }}</dd>
                <dt class="col-sm-4">Objeto</dt>
                <dd class="col-sm-8">{{ $preContrato->objeto ?? '—' }}</dd>
                <dt class="col-sm-4">Contratada</dt>
                <dd class="col-sm-8">{{ $preContrato->empresa_razao_social ?? '—' }}</dd>
                <dt class="col-sm-4">CNPJ</dt>
                <dd class="col-sm-8">{{ $preContrato->empresa_cnpj ?? '—' }}</dd>
              </dl>
            </div>
            <div class="col-md-6">
              <dl class="row mb-0">
                <dt class="col-sm-4">Início</dt>
                <dd class="col-sm-8">{{ optional($preContrato->data_inicio_vigencia ?? $preContrato->data_inicio ?? $preContrato->data_assinatura)->format('d/m/Y') ?? '—' }}</dd>
                <dt class="col-sm-4">Fim</dt>
                <dd class="col-sm-8">{{ optional($preContrato->data_fim_vigencia ?? $preContrato->data_fim)->format('d/m/Y') ?? '—' }}</dd>
                <dt class="col-sm-4">Valor Global</dt>
                <dd class="col-sm-8">{{ $preContrato->valor_global !== null ? ('R$ ' . number_format((float)$preContrato->valor_global, 2, ',', '.')) : '—' }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
      @endif
      <form method="POST" action="{{ route('empenhos.store') }}">
        @csrf

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Número</label>
            <input type="text" name="numero" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Empresa</label>
            <select name="empresa_id" class="form-select" required>
              <option value="">Selecione...</option>
              @foreach($empresas as $empresa)
                <option value="{{ $empresa->id }}" {{ (string)$empresa->id === (string)($preEmpresaId ?? '') ? 'selected' : '' }}>{{ $empresa->razao_social }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Contrato</label>
            <select name="contrato_id" class="form-select" required>
              <option value="">Selecione...</option>
              @foreach($contratos as $contrato)
                <option value="{{ $contrato->id }}" {{ (string)$contrato->id === (string)($preContratoId ?? '') ? 'selected' : '' }}>{{ $contrato->numero }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Data</label>
            <input type="date" name="data_lancamento" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Processo</label>
            <input type="text" name="processo" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Programa de Trabalho</label>
            <input type="text" name="programa_trabalho" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Fonte de Recurso</label>
            <input type="text" name="fonte_recurso" class="form-control">
          </div>

          <div class="col-md-12 mt-4">
            <h5 class="fw-bold text-secondary"><i class="fas fa-list me-1"></i>Itens do Empenho</h5>
            <table class="table table-sm" id="tblItens">
              <thead><tr><th>Descrição</th><th>Qtd</th><th>Valor Unitário</th><th></th></tr></thead>
              <tbody></tbody>
            </table>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addItem"><i class="fas fa-plus"></i> Adicionar Item</button>
          </div>

          <div class="col-12 mt-4 d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-success px-4"><i class="fas fa-save me-1"></i>Cadastrar Nota de Empenho</button>
            <a href="{{ isset($preContratoId) && $preContratoId ? route('contratos.show', $preContratoId) : url()->previous() }}" class="btn btn-outline-secondary">
              <i class="fas fa-times me-1"></i> Cancelar
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
let linha = 0;
$('#addItem').on('click', function() {
  linha++;
  $('#tblItens tbody').append(`
    <tr>
      <td><input type="text" name="itens[${linha}][descricao]" class="form-control form-control-sm" required></td>
      <td><input type="number" name="itens[${linha}][quantidade]" step="0.01" class="form-control form-control-sm" required></td>
      <td><input type="text" name="itens[${linha}][valor_unitario]" class="form-control form-control-sm money-br-input" required></td>
      <td><button type="button" class="btn btn-sm btn-outline-danger delItem"><i class="fas fa-trash"></i></button></td>
    </tr>
  `);
});
$(document).on('click', '.delItem', function() {
  $(this).closest('tr').remove();
});
</script>
@endsection
