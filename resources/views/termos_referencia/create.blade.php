@extends('layouts.app')
@section('plugins.Summernote', true)
@section('title', 'Cadastrar Termo de Referência')

@section('content_body')

<div class="container-fluid">

<form action="{{ route('contratacoes.termos-referencia.store') }}" method="POST" class="shadow-sm bg-white rounded-4 p-3">
@csrf

{{-- ======================================================= --}}
{{--  TÍTULO E BARRA SUPERIOR --}}
{{-- ======================================================= --}}
<div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
    <h3 class="m-0 fw-semibold text-primary">
        <i class="fas fa-file-alt me-2"></i>Cadastrar Termo de Referência
    </h3>
    <a href="{{ route('contratacoes.termos-referencia.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

{{-- ======================================================= --}}
{{--  ABAS --}}
{{-- ======================================================= --}}
<ul class="nav nav-pills tr-nav" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-tab="geral">Geral</a></li>
    <li class="nav-item"><a class="nav-link" data-tab="especificacao">Especificação</a></li>
    <li class="nav-item"><a class="nav-link" data-tab="habilitacao">Habilitação</a></li>
    <li class="nav-item"><a class="nav-link" data-tab="modelo">Modelos e Orçamento</a></li>
    <li class="nav-item"><a class="nav-link" data-tab="itens">Itens</a></li>
    <li class="nav-item"><a class="nav-link" data-tab="entrega">Entrega / Recebimento</a></li>
    <li class="nav-item"><a class="nav-link" data-tab="penalidades">Penalidades</a></li>
    <li class="nav-item"><a class="nav-link" data-tab="contrato">Contrato</a></li>
</ul>

{{-- ======================================================= --}}
{{--  ABA 1 - GERAL --}}
{{-- ======================================================= --}}
<div class="tr-tab mt-4" data-tab="geral">

    <h5 class="section-title">Informações Gerais</h5>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tipo de TR</label>
            <select name="tipo_tr" class="form-select" required>
                <option value="">Selecione...</option>
                <option value="bens_comuns">Bens Comuns</option>
                <option value="servicos_sem_mao_de_obra_sem_prorrogacao">Serviços sem Mão-de-Obra / sem Prorrogação</option>
                <option value="bens_servicos_tecnologia" selected>Bens e Serviços de Tecnologia</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">PAE nº</label>
            <input type="text" name="pae_numero" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Cidade</label>
            <input type="text" name="cidade" class="form-control" value="{{ $session_cidade ?? 'Belém' }}" readonly>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-3">
            <label class="form-label">Data de Emissão</label>
            <input type="date" name="data_emissao" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Responsável</label>
            <input type="text" class="form-control" value="{{ $session_responsavel ?? '' }}" readonly>
        </div>

        <div class="col-md-3">
            <label class="form-label">Cargo</label>
            <input type="text" class="form-control" value="{{ $session_cargo ?? '' }}" readonly>
        </div>

        <div class="col-md-3">
            <label class="form-label">Matrícula</label>
            <input type="text" class="form-control" value="{{ $session_matricula ?? '' }}" readonly>
        </div>
    </div>
</div>


{{-- ======================================================= --}}
{{--  ABA 2 - ESPECIFICAÇÃO --}}
{{-- ======================================================= --}}
<div class="tr-tab mt-4 d-none" data-tab="especificacao">

    <h5 class="section-title">Especificação do Objeto</h5>

    <div class="mb-3">
        <label class="form-label fw-semibold">Definição do Objeto</label>
        <textarea name="objeto" class="form-control tr-editor" rows="6" required></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Fundamentação da Contratação</label>
        <textarea name="justificativa" class="form-control tr-editor" rows="6" required></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Fundamentação Legal</label>
        <textarea name="fundamentacao_legal_texto" class="form-control tr-editor" rows="4" required></textarea>
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="autoFund" name="auto_fundamentacao" value="1">
        <label for="autoFund" class="form-check-label">Gerar fundamentação com normas indexadas</label>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Descrição da Solução</label>
        <textarea name="escopo" class="form-control tr-editor" rows="6" required></textarea>
    </div>

</div>


{{-- ======================================================= --}}
{{--  ABA 3 - HABILITAÇÃO --}}
{{-- ======================================================= --}}
<div class="tr-tab mt-4 d-none" data-tab="habilitacao">

    <h5 class="section-title">Habilitação e Qualificações</h5>

    {{-- Habilitação Jurídica --}}
    <div class="mb-4">
        <h6 class="mb-2 text-primary fw-semibold">7.1 Habilitação Jurídica</h6>
        <div class="form-check">
            <input type="checkbox" name="habilitacao_juridica_existencia" class="form-check-input" id="hj1">
            <label for="hj1" class="form-check-label">Comprovação de existência jurídica</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="habilitacao_juridica_autorizacao" class="form-check-input" id="hj2">
            <label for="hj2" class="form-check-label">Autorização para exercício da atividade</label>
        </div>
    </div>

    {{-- Habilitação Técnica --}}
    <div class="mb-4">
        <h6 class="mb-2 text-primary fw-semibold">7.2 Habilitação Técnica</h6>
        <div class="form-check form-check-inline">
            <input type="radio" name="habilitacao_tecnica_exigida" value="1" class="form-check-input" id="htSim" required>
            <label for="htSim" class="form-check-label">Sim</label>
        </div>

        <div class="form-check form-check-inline">
            <input type="radio" name="habilitacao_tecnica_exigida" value="0" class="form-check-input" id="htNao" required>
            <label for="htNao" class="form-check-label">Não</label>
        </div>

        <div id="habilitacao_tecnica_campos" class="mt-3 d-none">
            <label class="form-label">Qual?</label>
            <textarea name="habilitacao_tecnica_qual" class="form-control form-control-sm"></textarea>
        </div>
    </div>

</div>


{{-- ======================================================= --}}
{{--  ABA 4 - MODELOS E ORÇAMENTO --}}
{{-- ======================================================= --}}
<div class="tr-tab mt-4 d-none" data-tab="modelo">

    <h5 class="section-title">Modelos e Orçamento</h5>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Valor Estimado</label>
            <div class="input-group">
                <span class="input-group-text">R$</span>
                <input type="text" name="valor_estimado" class="form-control input-money" readonly>
            </div>
            <small class="text-muted">Calculado automaticamente</small>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select" required>
                <option value="rascunho">Rascunho</option>
                <option value="em_analise">Em análise</option>
                <option value="finalizado">Finalizado</option>
            </select>
        </div>
    </div>

</div>


{{-- ======================================================= --}}
{{--  ABA 5 – ITENS --}}
{{-- ======================================================= --}}
<div class="tr-tab mt-4 d-none" data-tab="itens">
    <h5 class="section-title">Itens do Termo de Referência</h5>

    <div class="table-responsive">
        <table class="table table-striped" id="itens-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Descrição</th>
                    <th>Unid</th>
                    <th class="text-end">Qtd</th>
                    <th class="text-end">Vlr Unit (R$)</th>
                    <th class="text-end">Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="itens-body"></tbody>
        </table>
    </div>

    <div class="d-flex gap-2 mt-2">
        <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-plus me-1"></i>Adicionar Item
        </button>

        <button type="button" id="btn-clear-items" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-trash me-1"></i>Limpar Itens
        </button>
    </div>
</div>


{{-- ======================================================= --}}
{{--  ABA 6 – ENTREGA --}}
{{-- ======================================================= --}}
<div class="tr-tab mt-4 d-none" data-tab="entrega">

    <h5 class="section-title">Entrega e Recebimento</h5>

    <div class="mb-3">
        <label class="form-label fw-semibold">Forma de Entrega</label>
        <div class="form-check">
            <input type="radio" name="entrega_forma" value="total" class="form-check-input" id="efTotal" required>
            <label for="efTotal" class="form-check-label">Entrega Total</label>
        </div>
        <div class="form-check">
            <input type="radio" name="entrega_forma" value="parcelada" class="form-check-input" id="efParc" required>
            <label for="efParc" class="form-check-label">Entregas Parceladas</label>
        </div>
    </div>

</div>


{{-- ======================================================= --}}
{{--  ABA 7 – PENALIDADES --}}
{{-- ======================================================= --}}
<div class="tr-tab mt-4 d-none" data-tab="penalidades">

    <h5 class="section-title">Penalidades e Regras</h5>

    <div class="mb-3">
        <label class="form-label fw-semibold">Penalidades</label>
        <textarea name="penalidades" class="form-control tr-editor" rows="6" required></textarea>
    </div>

</div>


{{-- ======================================================= --}}
{{--  ABA 8 – CONTRATO --}}
{{-- ======================================================= --}}
<div class="tr-tab mt-4 d-none" data-tab="contrato">

    <h5 class="section-title">Informações Contratuais</h5>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Elaboração</label>
            <input type="text" name="assin_elaboracao" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Supervisor</label>
            <input type="text" name="assin_supervisor" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Ordenador de Despesas</label>
            <input type="text" name="assin_ordenador_despesas" class="form-control" required>
        </div>
    </div>
</div>



{{-- ======================================================= --}}
{{--  BOTÃO FINAL --}}
{{-- ======================================================= --}}
<div id="tr-submit-area" class="border-top pt-3 mt-4 text-end d-none">
    <button id="tr-submit" type="submit" class="btn btn-primary btn-lg px-4" disabled>
        <i class="fas fa-save me-1"></i>Salvar Termo
    </button>
</div>

</form>
</div>

@endsection

@push('scripts')
<script>
(function(){
  const form = document.querySelector('form[action*="termos-referencia"]');
  if(!form) return;
  const submitArea = document.getElementById('tr-submit-area');
  const submitBtn = document.getElementById('tr-submit');
  const requiredSelectors = [
    '[name="tipo_tr"]',
    '[name="pae_numero"]',
    '[name="data_emissao"]',
    '[name="objeto"]',
    '[name="justificativa"]',
    '[name="fundamentacao_legal_texto"]',
    '[name="escopo"]',
    '[name="status"]',
    '[name="penalidades"]',
    '[name="assin_elaboracao"]',
    '[name="assin_supervisor"]',
    '[name="assin_ordenador_despesas"]'
  ];
  const radiosHT = Array.from(document.querySelectorAll('input[name="habilitacao_tecnica_exigida"]'));
  const radiosEntrega = Array.from(document.querySelectorAll('input[name="entrega_forma"]'));
  const htQual = document.querySelector('[name="habilitacao_tecnica_qual"]');
  const htWrap = document.getElementById('habilitacao_tecnica_campos');
  const itensBody = document.getElementById('itens-body');
  function hasRadioChecked(list){ return list.some(r => r.checked); }
  function isFilled(el){ return !!(el && String(el.value || '').trim()); }
  function validate(){
    let ok = true;
    requiredSelectors.forEach(sel => {
      const el = document.querySelector(sel);
      if(!el) return;
      const filled = isFilled(el);
      el.classList.toggle('is-invalid', !filled);
      if(!filled) ok = false;
    });
    const htChecked = hasRadioChecked(radiosHT);
    if(!htChecked) ok = false;
    if (htChecked && (document.getElementById('htSim').checked)) {
      htWrap?.classList.remove('d-none');
      const need = isFilled(htQual);
      htQual?.classList.toggle('is-invalid', !need);
      if(!need) ok = false;
    } else {
      htWrap?.classList.add('d-none');
      htQual?.classList.remove('is-invalid');
    }
    const entregaChecked = hasRadioChecked(radiosEntrega);
    if(!entregaChecked) ok = false;
    const hasItens = itensBody && itensBody.children.length > 0;
    if(!hasItens) ok = false;
    submitArea.classList.toggle('d-none', !ok);
    submitBtn.disabled = !ok;
  }
  form.addEventListener('input', validate);
  form.addEventListener('change', validate);
  document.addEventListener('turbo:load', validate);
  document.addEventListener('DOMContentLoaded', validate);
})();
</script>
@endpush
