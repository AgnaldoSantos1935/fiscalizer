@extends('layouts.app')
@section('title', 'Novo Contrato')

@section('content_body')
@section('breadcrumb')
  @include('layouts.components.breadcrumbs', [
    'trail' => [
      ['label' => 'Contratos', 'icon' => 'fas fa-file-contract', 'url' => route('contratos.index')],
      ['label' => 'Novo Contrato']
    ]
  ])
@endsection
<h2 class="mb-4">Cadastrar Novo Contrato</h2>

<form action="{{ route('contratos.store') }}" method="POST" class="card p-4 shadow-sm">
  @csrf
  <!-- Upload de PDF removido: anexar somente na tela de detalhes do contrato -->
  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Número *</label>
      <input type="text" name="numero" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Valor Global (R$) *</label>
      <input type="text" name="valor_global" class="form-control money-br-input" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Empresa Contratada *</label>
      <div class="input-group">
      <select name="contratada_id" id="contratada_id" class="form-select" required>
        <option value="">Selecione...</option>
        @foreach($empresas as $empresa)
          <option value="{{ $empresa->id }}" @if(request('empresa_id') == $empresa->id) selected @endif>{{ $empresa->razao_social }}</option>
        @endforeach
      </select>
      <button type="button" class="btn btn-outline-primary" id="btnVerificarCnpj"><i class="fas fa-search"></i></button>
      <a href="{{ route('empresas.create', ['return' => 'contratos.create']) }}" class="btn btn-outline-success" id="btnCadastrarEmpresa"><i class="fas fa-plus"></i></a>
      </div>
      <small class="text-muted">Use a lupa para verificar por CNPJ e o + para cadastrar nova empresa em outra tela.</small>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Objeto *</label>
    <textarea name="objeto" rows="3" class="form-control" required></textarea>
  </div>
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Data Início</label>
      <input type="date" name="data_inicio" class="form-control">
    </div>
    <div class="col-md-6">
      <label class="form-label">Data Fim</label>
      <input type="date" name="data_fim" class="form-control">
    </div>
  </div>

  <h5 class="mt-3">Fiscais e Gestor</h5>
  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Fiscal Técnico</label>
      <select name="fiscal_tecnico_id" class="form-select">
        <option value="">-- Selecione --</option>
        @foreach($pessoas as $p)
          <option value="{{ $p->id }}">{{ $p->nome_completo }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Suplente (Fiscal Técnico)</label>
      <select name="suplente_fiscal_tecnico_id" class="form-select">
        <option value="">-- Selecione --</option>
        @foreach($pessoas as $p)
          <option value="{{ $p->id }}">{{ $p->nome_completo }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Gestor do Contrato</label>
      <select name="gestor_id" class="form-select">
        <option value="">-- Selecione --</option>
        @foreach($pessoas as $p)
          <option value="{{ $p->id }}">{{ $p->nome_completo }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Fiscal Administrativo</label>
      <select name="fiscal_administrativo_id" class="form-select">
        <option value="">-- Selecione --</option>
        @foreach($pessoas as $p)
          <option value="{{ $p->id }}">{{ $p->nome_completo }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Suplente (Fiscal Administrativo)</label>
      <select name="suplente_fiscal_administrativo_id" class="form-select">
        <option value="">-- Selecione --</option>
        @foreach($pessoas as $p)
          <option value="{{ $p->id }}">{{ $p->nome_completo }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="text-end">
    <a href="{{ route('contratos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button id="btnsalvar" type="submit" class="btn btn-success">Salvar</button>
  </div>
</form>
@endsection
@section('css')

@endsection
@section('js')
<!-- Sem JS de extração: PDF será anexado na página de detalhes do contrato -->
<script>
  (function(){
    function showToast(type, message){
      var toastEl, msgEl;
      if(type === 'success'){ toastEl = document.getElementById('toastSuccess'); msgEl = document.getElementById('toastSuccessMsg'); }
      else { toastEl = document.getElementById('toastError'); msgEl = document.getElementById('toastErrorMsg'); }
      if(msgEl) msgEl.textContent = message;
      if(toastEl && typeof bootstrap !== 'undefined' && bootstrap.Toast){ try { new bootstrap.Toast(toastEl).show(); } catch(e){} }
    }
    // Botão "+": abre diretamente a tela de cadastro de empresa
    const btnCadastrar = document.getElementById('btnCadastrarEmpresa');
    const btnVerificar = document.getElementById('btnVerificarCnpj');
    const selectEmpresa = document.getElementById('contratada_id');
    // Sem interceptação: navegação direta via href com return=contratos.create
    if(btnVerificar){
      btnVerificar.addEventListener('click', async ()=>{
        const cnpj = prompt('Informe o CNPJ (somente números ou com máscara):');
        if(!cnpj) return;
        try{
          const url = "{{ route('empresas.verificar') }}" + '?cnpj=' + encodeURIComponent(cnpj);
          const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
          const j = await r.json();
          if(j.found){
            const opt = Array.from(selectEmpresa.options).find(o => o.textContent.trim() === j.data.razao_social || o.value == j.data.id);
            if(!opt){
              const o = document.createElement('option'); o.value = j.data.id; o.textContent = j.data.razao_social; selectEmpresa.appendChild(o);
            }
            selectEmpresa.value = j.data.id;
            // Removido alert: seleção silenciosa
          } else {
            const clean = (cnpj||'').replace(/\D+/g,'');
            const urlCreate = "{{ route('empresas.create') }}" + '?return=contratos.create' + (clean ? ('&cnpj=' + encodeURIComponent(clean)) : '');
            window.location.href = urlCreate;
          }
        } catch(err){ console.error(err); /* alert removido */ }
      });
    }
  })();
</script>
@endsection
