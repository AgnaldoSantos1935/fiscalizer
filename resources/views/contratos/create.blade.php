@extends('layouts.app')
@section('title', 'Cadastrar Contrato')

@section('content_body')
@section('breadcrumb')
  @include('layouts.components.breadcrumbs', [
    'trail' => [
      ['label' => 'Contratos', 'icon' => 'fas fa-file-contract', 'url' => route('contratos.index')],
      ['label' => 'Cadastrar Contrato']
    ]
  ])
@endsection
<h2 class="mb-4">Cadastrar Contrato</h2>
@if(session('success'))
  <div class="alert alert-success d-flex align-items-center" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <div>{{ session('success') }}</div>
  </div>
@endif

<form action="{{ route('contratos.store') }}" method="POST" class="card p-4 shadow-sm" id="formContrato">
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

  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Processo Origem</label>
      <input type="text" name="processo_origem" class="form-control" value="{{ old('processo_origem') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Modalidade</label>
      <input type="text" name="modalidade" class="form-control" value="{{ old('modalidade') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="Ativo" @selected(old('status')==='Ativo')>Ativo</option>
        <option value="Suspenso" @selected(old('status')==='Suspenso')>Suspenso</option>
        <option value="Encerrado" @selected(old('status')==='Encerrado')>Encerrado</option>
      </select>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Objeto Resumido</label>
    <input type="text" name="objeto_resumido" class="form-control" value="{{ old('objeto_resumido') }}">
  </div>

  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Data Assinatura</label>
      <input type="date" name="data_assinatura" class="form-control" value="{{ old('data_assinatura') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Início Vigência</label>
      <input type="date" name="data_inicio_vigencia" class="form-control" value="{{ old('data_inicio_vigencia') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Fim Vigência</label>
      <input type="date" name="data_fim_vigencia" class="form-control" value="{{ old('data_fim_vigencia') }}">
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Valor Mensal (R$)</label>
      <input type="text" name="valor_mensal" class="form-control money-br-input" value="{{ old('valor_mensal') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Quantidade de Meses</label>
      <input type="number" name="quantidade_meses" class="form-control" min="1" value="{{ old('quantidade_meses') }}">
    </div>
  </div>

  <h5 class="mt-3">Empresa (campos diretos)</h5>
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Razão Social</label>
      <input type="text" name="empresa_razao_social" class="form-control" value="{{ old('empresa_razao_social') }}">
    </div>
    <div class="col-md-3">
      <label class="form-label">CNPJ</label>
      <input type="text" name="empresa_cnpj" id="empresa_cnpj" class="form-control cnpj-input" value="{{ old('empresa_cnpj') }}">
    </div>
    <div class="col-md-3">
      <label class="form-label">E-mail</label>
      <input type="email" name="empresa_email" class="form-control" value="{{ old('empresa_email') }}">
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-8">
      <label class="form-label">Endereço</label>
      <input type="text" name="empresa_endereco" class="form-control" value="{{ old('empresa_endereco') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Representante</label>
      <input type="text" name="empresa_representante" class="form-control" value="{{ old('empresa_representante') }}">
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Contato</label>
      <input type="text" name="empresa_contato" class="form-control" value="{{ old('empresa_contato') }}">
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
    <div class="col-md-4">
      <label class="form-label">Fiscal Técnico (nome)</label>
      <input type="text" name="fiscal_tecnico" class="form-control" value="{{ old('fiscal_tecnico') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Fiscal Administrativo (nome)</label>
      <input type="text" name="fiscal_administrativo" class="form-control" value="{{ old('fiscal_administrativo') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Gestor (nome)</label>
      <input type="text" name="gestor" class="form-control" value="{{ old('gestor') }}">
    </div>
  </div>

  <h5 class="mt-3">Riscos</h5>
  <div class="row mb-3">
    <div class="col-md-3">
      <label class="form-label">Score</label>
      <input type="number" name="risco_score" class="form-control" min="0" max="100" value="{{ old('risco_score') }}">
    </div>
    <div class="col-md-3">
      <label class="form-label">Nível</label>
      <input type="text" name="risco_nivel" class="form-control" value="{{ old('risco_nivel') }}">
    </div>
    <div class="col-md-6">
      <label class="form-label">Detalhes (JSON)</label>
      <textarea name="risco_detalhes_json" id="risco_detalhes_json" class="form-control" rows="2">{{ old('risco_detalhes_json') }}</textarea>
    </div>
  </div>

  <h5 class="mt-3">Campos JSON</h5>
  <div class="row mb-3">
    <div class="col-md-12">
      <h5>Itens Contratados</h5>
      <div class="row g-2 align-items-end mb-2">
        <div class="col-md-4">
          <label class="form-label">Descrição</label>
          <input type="text" id="item_desc" class="form-control">
        </div>
        <div class="col-md-2">
          <label class="form-label">Unidade</label>
          <input type="text" id="item_unid" class="form-control">
        </div>
        <div class="col-md-2">
          <label class="form-label">Quantidade</label>
          <input type="number" id="item_qtd" class="form-control" step="0.01" min="0">
        </div>
        <div class="col-md-2">
          <label class="form-label">Meses</label>
          <input type="number" id="item_meses" class="form-control" min="0">
        </div>
        <div class="col-md-2">
          <label class="form-label">Valor Unitário (R$)</label>
          <input type="text" id="item_vu" class="form-control money-br-input">
        </div>
        <div class="col-md-2">
          <button type="button" id="addItem" class="btn btn-primary w-100">Cadastrar item</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-striped align-middle" id="itemsTable">
          <thead>
            <tr>
              <th>Descrição</th>
              <th>Unidade</th>
              <th>Qtd</th>
              <th>Meses</th>
              <th>V.Unitário (R$)</th>
              <th>Alíquota (%)</th>
              <th>Desconto (%)</th>
              <th>V.Total (R$)</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="itemsTableBody"></tbody>
          <tfoot>
            <tr>
              <th colspan="4" class="text-end">Total</th>
              <th id="itemsTotalBR">R$ 0,00</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <textarea name="obrigacoes_contratada" id="obrigacoes_contratada" class="form-control d-none">{{ old('obrigacoes_contratada') }}</textarea>
      <textarea name="obrigacoes_contratante" id="obrigacoes_contratante" class="form-control d-none">{{ old('obrigacoes_contratante') }}</textarea>
      <textarea name="itens_fornecimento" id="itens_fornecimento" class="form-control d-none">{{ old('itens_fornecimento') }}</textarea>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Anexos Detectados (JSON)</label>
      <textarea name="anexos_detectados" id="anexos_detectados" class="form-control" rows="2">{{ old('anexos_detectados') }}</textarea>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Cláusulas (JSON)</label>
      <textarea name="clausulas" id="clausulas" class="form-control" rows="2">{{ old('clausulas') }}</textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label">Riscos Detectados (JSON)</label>
      <textarea name="riscos_detectados" id="riscos_detectados" class="form-control" rows="2">{{ old('riscos_detectados') }}</textarea>
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
  <button id="btnsalvar" type="submit" class="btn btn-success">Cadastrar Contrato</button>
  </div>
</form>
@endsection
@section('css')

@endsection
@section('js')
<!-- Sem JS de extração: PDF será anexado na página de detalhes do contrato -->
<script>
  (function(){
    const form = document.getElementById('formContrato');
    const moneyInputs = document.querySelectorAll('.money-br-input');
    function brToDecimal(v){ if(v==null) return null; let s=String(v).replace(/[^\d,\.]/g,''); s=s.replace(/\./g,''); s=s.replace(/,/g,'.'); return s; }
    function isValidMoneyBR(v){ const s=String(v).trim(); return /^\d{1,3}(\.\d{3})*(,\d{2})$|^\d+(,\d{2})$|^\d+$/.test(s); }
    function isValidJSON(text){ if(!text || !String(text).trim()) return true; try{ JSON.parse(text); return true; }catch(e){ return false; } }
    function maskCNPJ(v){ const d=(v||'').replace(/\D/g,'').slice(0,14); let out=''; for(let i=0;i<d.length;i++){ out+=d[i]; if(i===1) out+='.'; if(i===4) out+='.'; if(i===7) out+='/'; if(i===11) out+='-'; } return out; }
    function isValidCnpjDigits(c){ const d=(c||'').replace(/\D/g,''); if(d.length!==14) return false; if(/^(\d)\1{13}$/.test(d)) return false; const calc=(base,len)=>{ let sum=0,pos=len-7; for(let i=0;i<len;i++){ sum+=parseInt(base[i],10)*pos; pos--; if(pos<2) pos=9; } const res=sum%11; return res<2?0:(11-res); }; const d1=calc(d,12); if(parseInt(d[12],10)!==d1) return false; const d2=calc(d,13); return parseInt(d[13],10)===d2; }

    const empresaCnpj = document.getElementById('empresa_cnpj');
    if(empresaCnpj){ empresaCnpj.addEventListener('input', function(){ this.value = maskCNPJ(this.value); }); }

    moneyInputs.forEach(inp=>{
      inp.addEventListener('blur', function(){
        const val = this.value;
        if(val && !isValidMoneyBR(val)){
          this.classList.add('is-invalid');
        } else {
          this.classList.remove('is-invalid');
        }
      });
    });

    if(form){
      form.addEventListener('submit', function(e){
        let ok = true;
        moneyInputs.forEach(inp=>{
          const val = inp.value;
          if(val){
            if(!isValidMoneyBR(val)) ok=false;
            inp.value = brToDecimal(val);
          }
        });
        const jsonFields = ['risco_detalhes_json','obrigacoes_contratada','obrigacoes_contratante','itens_fornecimento','anexos_detectados','clausulas','riscos_detectados'];
        jsonFields.forEach(id=>{
          const el = document.getElementById(id);
          if(el && !isValidJSON(el.value)) ok=false;
        });
        if(empresaCnpj && empresaCnpj.value){ if(!isValidCnpjDigits(empresaCnpj.value)) ok=false; }
        try{
          const data = JSON.stringify(ITEMS.map(x=>({descricao:x.descricao, unidade:x.unidade, quantidade:x.quantidade, meses:x.meses, valor_unitario:x.valor_unitario})));
          const hidden = document.getElementById('itens_fornecimento');
          if(hidden) hidden.value = data;
        }catch(err){ ok=false; }
        if(!ok){ e.preventDefault(); alert('Verifique os campos: formatos inválidos encontrados.'); }
      });
    }

    const redirectTo = "{{ session('redirect_to') }}";
    if(redirectTo && typeof window !== 'undefined' && {{ session('success') ? 'true' : 'false' }}){
      setTimeout(function(){ window.location.href = redirectTo; }, 1800);
    }
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

    const ITEMS = [];
    const $body = document.getElementById('itemsTableBody');
    const $total = document.getElementById('itemsTotalBR');
    const $numero = document.querySelector('input[name="numero"]');
    const $valorGlobal = document.querySelector('input[name="valor_global"]');
    const $desc = document.getElementById('item_desc');
    const $unid = document.getElementById('item_unid');
    const $qtd = document.getElementById('item_qtd');
    const $meses = document.getElementById('item_meses');
    const $vu = document.getElementById('item_vu');
    const $add = document.getElementById('addItem');
    function fmt(v){ try{ return (Number(v)||0).toLocaleString('pt-BR',{minimumFractionDigits:2, maximumFractionDigits:2}); }catch(e){ return '0,00'; } }
    function toDecBR(v){ if(v==null) return 0; let s=String(v).replace(/[\D]+/g,''); if(/,/.test(String(v))||/\./.test(String(v))){ s=String(v).replace(/\./g,'').replace(/,/g,'.'); } else { s=String(v); } return Number(s)||0; }
    function render(){
      $body.innerHTML='';
      let total = 0;
      ITEMS.forEach((it, idx)=>{
        const tr = document.createElement('tr');
        const td1 = document.createElement('td'); const inp1 = document.createElement('input'); inp1.type='text'; inp1.className='form-control form-control-sm'; inp1.value=it.descricao; inp1.oninput=function(){ it.descricao=this.value; };
        const td2 = document.createElement('td'); const inp2 = document.createElement('input'); inp2.type='text'; inp2.className='form-control form-control-sm'; inp2.value=it.unidade; inp2.oninput=function(){ it.unidade=this.value; };
        const td3 = document.createElement('td'); const inp3 = document.createElement('input'); inp3.type='number'; inp3.step='0.01'; inp3.min='0'; inp3.className='form-control form-control-sm'; inp3.value=it.quantidade; inp3.oninput=function(){ it.quantidade=Number(this.value)||0; updateTotals(); };
        const td4b = document.createElement('td'); const inp4b = document.createElement('input'); inp4b.type='number'; inp4b.step='1'; inp4b.min='0'; inp4b.className='form-control form-control-sm'; inp4b.value=it.meses ?? 0; inp4b.oninput=function(){ it.meses=Number(this.value)||0; updateTotals(); };
        const td4 = document.createElement('td'); const inp4 = document.createElement('input'); inp4.type='text'; inp4.className='form-control form-control-sm money-br-input'; inp4.value=it.valor_unitario_br||it.valor_unitario; inp4.onblur=function(){ const val=this.value; if(val){ if(!isValidMoneyBR(val)){ this.classList.add('is-invalid'); } else { this.classList.remove('is-invalid'); } } it.valor_unitario_br=val; it.valor_unitario=toDecBR(val); updateTotals(); };
        const td5 = document.createElement('td'); const inp5 = document.createElement('input'); inp5.type='number'; inp5.step='0.01'; inp5.min='0'; inp5.className='form-control form-control-sm'; inp5.value=it.aliquota_percent ?? 0; inp5.oninput=function(){ it.aliquota_percent = Number(this.value)||0; updateTotals(); };
        const td6 = document.createElement('td'); const inp6 = document.createElement('input'); inp6.type='number'; inp6.step='0.01'; inp6.min='0'; inp6.className='form-control form-control-sm'; inp6.value=it.desconto_percent ?? 0; inp6.oninput=function(){ it.desconto_percent = Number(this.value)||0; updateTotals(); };
        const td7 = document.createElement('td'); td7.textContent='R$ '+fmt(calcItemTotal(it)); td7.dataset.idx=idx;
        const td8 = document.createElement('td'); const rem = document.createElement('button'); rem.type='button'; rem.className='btn btn-sm btn-outline-danger'; rem.textContent='Remover'; rem.onclick=function(){ ITEMS.splice(idx,1); render(); };
        td1.appendChild(inp1); td2.appendChild(inp2); td3.appendChild(inp3); td4b.appendChild(inp4b); td4.appendChild(inp4); td5.appendChild(inp5); td6.appendChild(inp6); td8.appendChild(rem);
        tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3); tr.appendChild(td4b); tr.appendChild(td4); tr.appendChild(td5); tr.appendChild(td6); tr.appendChild(td7); tr.appendChild(td8);
        $body.appendChild(tr);
        total += calcItemTotal(it);
      });
      $total.textContent = 'R$ '+fmt(total);
      if($valorGlobal){ $valorGlobal.value = fmt(total); }
    }
    function updateTotals(){
      let total = 0;
      Array.from($body.querySelectorAll('tr')).forEach((tr,i)=>{
        const it = ITEMS[i];
        const td = tr.children[7];
        const v = calcItemTotal(it);
        td.textContent = 'R$ '+fmt(v);
        total += v;
      });
      $total.textContent = 'R$ '+fmt(total);
      if($valorGlobal){ $valorGlobal.value = fmt(total); }
    }
    if($add){
      $add.addEventListener('click', function(){
        const d = ($desc.value||'').trim();
        const u = ($unid.value||'').trim();
        const q = Number($qtd.value)||0;
        const vuBr = ($vu.value||'').trim();
        if(!d || !u || !q || !vuBr) return;
        if(!isValidMoneyBR(vuBr)) return;
        const vu = toDecBR(vuBr);
        ITEMS.push({descricao:d, unidade:u, quantidade:q, meses:Number($meses.value)||0, valor_unitario:vu, valor_unitario_br:vuBr, aliquota_percent:0, desconto_percent:0});
        $desc.value=''; $unid.value=''; $qtd.value=''; $meses.value=''; $vu.value='';
        render();
      });
    }
    function calcItemTotal(it){
      const meses = (Number(it.meses)||0) > 0 ? Number(it.meses) : 1;
      const base = (it.quantidade||0)*(it.valor_unitario||0)*meses;
      const aliq = (Number(it.aliquota_percent)||0)/100;
      const desc = (Number(it.desconto_percent)||0)/100;
      const total = base * (1 + aliq) - base * desc;
      return total < 0 ? 0 : total;
    }
  })();
</script>
@endsection
