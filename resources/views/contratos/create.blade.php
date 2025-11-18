@extends('layouts.app')
@section('title', 'Novo Contrato')

@section('content')
@section('breadcrumb')
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
      <li class="breadcrumb-item">
        <a href="{{ route('contratos.index') }}" class="text-decoration-none text-primary fw-semibold">
          <i class="fas fa-file-contract me-1"></i> Contratos
        </a>
      </li>
      <li class="breadcrumb-item active text-secondary fw-semibold">Novo Contrato</li>
    </ol>
    </nav>
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
          <option value="{{ $empresa->id }}">{{ $empresa->razao_social }}</option>
        @endforeach
      </select>
      <button type="button" class="btn btn-outline-primary" id="btnVerificarCnpj"><i class="fas fa-search"></i></button>
      <button type="button" class="btn btn-outline-success" id="btnNovaEmpresa" data-bs-toggle="modal" data-bs-target="#modalNovaEmpresa"><i class="fas fa-plus"></i></button>
      </div>
      <small class="text-muted">Use a lupa para verificar por CNPJ e o + para cadastrar nova empresa.</small>
    </div>
  </div>

  <div class="modal fade" id="modalNovaEmpresa" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cadastrar Nova Empresa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Razão Social</label>
              <input type="text" id="novaRazao" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">CNPJ</label>
              <input type="text" id="novoCnpj" class="form-control cnpj-input" placeholder="00.000.000/0000-00" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">CEP</label>
              <input type="text" id="novoCep" class="form-control cep-input" placeholder="00000-000">
            </div>
            <div class="col-md-5">
              <label class="form-label">Logradouro</label>
              <input type="text" id="novoLogradouro" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Número</label>
              <input type="text" id="novoNumero" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Complemento</label>
              <input type="text" id="novoComplemento" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Bairro</label>
              <input type="text" id="novoBairro" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Cidade</label>
              <input type="text" id="novoCidade" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">UF</label>
              <input type="text" id="novoUf" class="form-control" maxlength="2">
            </div>
            <div class="col-md-6">
              <label class="form-label">Contato</label>
              <input type="text" id="novoContato" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" id="novoEmail" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" id="btnSalvarEmpresa"><i class="fas fa-save me-1"></i>Salvar</button>
        </div>
      </div>
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
    const modalEl = document.getElementById('modalNovaEmpresa');
    let modal = null;
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      try { modal = new bootstrap.Modal(modalEl); } catch(e) { modal = null; }
    }
    const btnNova = document.getElementById('btnNovaEmpresa');
    const btnVerificar = document.getElementById('btnVerificarCnpj');
    const selectEmpresa = document.getElementById('contratada_id');
    if(btnNova && modal){ btnNova.addEventListener('click', ()=> modal.show()); }
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
            alert('Empresa encontrada. Contratos cadastrados: ' + (j.contratos_count ?? 0));
          } else {
            alert('Empresa não encontrada. Você pode cadastrá-la agora.');
            if(modal) modal.show();
          }
        } catch(err){ console.error(err); alert('Falha ao verificar CNPJ'); }
      });
    }
    const btnSalvarEmpresa = document.getElementById('btnSalvarEmpresa');
    if(btnSalvarEmpresa){
      btnSalvarEmpresa.addEventListener('click', async ()=>{
        const btn = btnSalvarEmpresa;
        const data = {
          razao_social: document.getElementById('novaRazao').value,
          cnpj: (document.getElementById('novoCnpj').value || '').replace(/\D+/g,''),
          cep: document.getElementById('novoCep').value,
          logradouro: document.getElementById('novoLogradouro').value,
          numero: document.getElementById('novoNumero').value,
          complemento: document.getElementById('novoComplemento').value,
          bairro: document.getElementById('novoBairro').value,
          cidade: document.getElementById('novoCidade').value,
          uf: document.getElementById('novoUf').value,
          contato: document.getElementById('novoContato').value,
          email: document.getElementById('novoEmail').value,
        };
        try{
          btn.disabled = true;
          const r = await fetch('{{ route('empresas.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(data)
          });
          let j = null;
          try { j = await r.json(); } catch(e) { j = null; }
          if(r.ok && j && j.success){
            const o = document.createElement('option'); o.value = j.data.id; o.textContent = j.data.razao_social; selectEmpresa.appendChild(o);
            selectEmpresa.value = j.data.id;
            if(modal){ modal.hide(); }
            showToast('success', 'Empresa cadastrada com sucesso!');
          } else {
            let msg = 'Falha ao cadastrar empresa.';
            if(j){
              if(j.errors){
                const flat = Object.values(j.errors).flat();
                msg = flat.join('\n');
              } else if(j.message){
                msg = j.message;
              }
            } else if(!r.ok) {
              msg = 'Erro '+r.status+' ao cadastrar empresa';
            }
            alert(msg);
          }
        } catch(err){ console.error(err); alert('Erro ao cadastrar empresa'); }
        finally { btn.disabled = false; }
      });
    }
    // CEP lookup
    (function(){
      function maskCEP(v){
        var d = (v||'').replace(/\D/g,'').slice(0,8);
        if(d.length > 5) return d.slice(0,5)+'-'+d.slice(5);
        return d;
      }
      async function viaCEP(cep){
        var d = (cep||'').replace(/\D/g,'');
        if(d.length !== 8) return null;
        const r = await fetch('https://viacep.com.br/ws/'+d+'/json/');
        const j = await r.json();
        if(j && !j.erro) return j;
        return null;
      }
      const novoCep = document.getElementById('novoCep');
      if(novoCep){
        novoCep.addEventListener('input', function(){ novoCep.value = maskCEP(novoCep.value); });
        novoCep.addEventListener('blur', async function(){
          const data = await viaCEP(novoCep.value);
          if(data){
            document.getElementById('novoLogradouro').value = data.logradouro || '';
            document.getElementById('novoBairro').value = data.bairro || '';
            document.getElementById('novoCidade').value = data.localidade || '';
            document.getElementById('novoUf').value = (data.uf || '').toUpperCase();
          }
        });
      }
    })();
  })();
</script>
@endsection
