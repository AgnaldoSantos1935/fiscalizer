@extends('layouts.app')
@section('title', 'Editar Contrato')

@section('content_body')
@section('breadcrumb')
  @include('layouts.components.breadcrumbs', [
    'trail' => [
      ['label' => 'Contratos', 'icon' => 'fas fa-file-contract', 'url' => route('contratos.index')],
      ['label' => 'Editar Contrato']
    ]
  ])
@endsection
<h2 class="mb-4">Editar Contrato</h2>
@if(session('success'))
  <div class="alert alert-success d-flex align-items-center" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <div>{{ session('success') }}</div>
  </div>
@endif

<form action="{{ route('contratos.update', $contrato->id) }}" method="POST" class="card p-4 shadow-sm" id="formContratoEdit">
  @csrf
  @method('PUT')
  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Número *</label>
      <input type="text" name="numero" class="form-control" value="{{ $contrato->numero }}" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Valor Global (R$)</label>
      <input type="text" name="valor_global" class="form-control money-br-input" value="{{ $contrato->valor_global ? number_format($contrato->valor_global,2,',','.') : '' }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Empresa Contratada *</label>
      <input type="text" name="empresa_razao_social" class="form-control" value="{{ old('empresa_razao_social', optional($contrato->contratada)->razao_social ?? $contrato->empresa_razao_social) }}">

    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Objeto *</label>
    <textarea name="objeto" rows="3" class="form-control" required>{{ $contrato->objeto }}</textarea>
  </div>

  <h5 class="mt-3">Itens Contratados</h5>
  <div class="row g-2 align-items-end mb-2">
    <div class="col-md-4">
      <label class="form-label">Descrição</label>
      <input type="text" id="item_desc_e" class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label">Unidade</label>
      <input type="text" id="item_unid_e" class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label">Quantidade</label>
      <input type="number" id="item_qtd_e" class="form-control" step="0.01" min="0">
    </div>
    <div class="col-md-2">
      <label class="form-label">Meses</label>
      <input type="number" id="item_meses_e" class="form-control" min="0">
    </div>
    <div class="col-md-2">
      <label class="form-label">Valor Unitário (R$)</label>
      <input type="text" id="item_vu_e" class="form-control money-br-input">
    </div>
    <div class="col-md-2">
      <button type="button" id="addItemE" class="btn btn-primary w-100">Cadastrar item</button>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-sm table-striped align-middle" id="itemsTableE">
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
      <tbody id="itemsTableBodyE"></tbody>
      <tfoot>
        <tr>
          <th colspan="6" class="text-end">Total</th>
          <th id="itemsTotalBRE">R$ 0,00</th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </div>
  <textarea name="itens_fornecimento" id="itens_fornecimento_e" class="form-control d-none"></textarea>

  <div class="text-end mt-3">
    <a href="{{ route('contratos.show', $contrato->id) }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-success">Salvar</button>
  </div>
</form>
@endsection

@section('js')
<script>
(function(){
  const form = document.getElementById('formContratoEdit');
  const moneyInputs = document.querySelectorAll('.money-br-input');
  function isValidMoneyBR(v){ const s=String(v).trim(); return /^\d{1,3}(\.\d{3})*(,\d{2})$|^\d+(,\d{2})$|^\d+$/.test(s); }
  function toDecBR(v){ if(v==null) return 0; let s=String(v).replace(/\./g,'').replace(/,/g,'.'); return Number(s)||0; }
  function fmt(v){ try{ return (Number(v)||0).toLocaleString('pt-BR',{minimumFractionDigits:2, maximumFractionDigits:2}); }catch(e){ return '0,00'; } }

  const ITEMS = (@json($itensJs)) || [];

  const $body = document.getElementById('itemsTableBodyE');
  const $total = document.getElementById('itemsTotalBRE');
  const $valorGlobal = document.querySelector('input[name="valor_global"]');
  const $desc = document.getElementById('item_desc_e');
  const $unid = document.getElementById('item_unid_e');
  const $qtd = document.getElementById('item_qtd_e');
  const $meses = document.getElementById('item_meses_e');
  const $vu = document.getElementById('item_vu_e');
  const $add = document.getElementById('addItemE');

  function calcItemTotal(it){
    const meses = (Number(it.meses)||0) > 0 ? Number(it.meses) : 1;
    const base = (it.quantidade||0)*(it.valor_unitario||0)*meses;
    const aliq = (Number(it.aliquota_percent)||0)/100;
    const desc = (Number(it.desconto_percent)||0)/100;
    const total = base * (1 + aliq) - base * desc;
    return total < 0 ? 0 : total;
  }
  function render(){
    $body.innerHTML='';
    let total = 0;
    ITEMS.forEach((it, idx)=>{
      const tr = document.createElement('tr');
      const td1 = document.createElement('td'); const inp1 = document.createElement('input'); inp1.type='text'; inp1.className='form-control form-control-sm'; inp1.value=it.descricao||''; inp1.oninput=function(){ it.descricao=this.value; };
      const td2 = document.createElement('td'); const inp2 = document.createElement('input'); inp2.type='text'; inp2.className='form-control form-control-sm'; inp2.value=it.unidade||''; inp2.oninput=function(){ it.unidade=this.value; };
      const td3 = document.createElement('td'); const inp3 = document.createElement('input'); inp3.type='number'; inp3.step='0.01'; inp3.min='0'; inp3.className='form-control form-control-sm'; inp3.value=it.quantidade||0; inp3.oninput=function(){ it.quantidade=Number(this.value)||0; updateTotals(); };
      const td4 = document.createElement('td'); const inp4 = document.createElement('input'); inp4.type='text'; inp4.className='form-control form-control-sm money-br-input'; inp4.value=it.valor_unitario_br||it.valor_unitario||''; inp4.onblur=function(){ const val=this.value; if(val){ if(!isValidMoneyBR(val)){ this.classList.add('is-invalid'); } else { this.classList.remove('is-invalid'); } } it.valor_unitario_br=val; it.valor_unitario=toDecBR(val); updateTotals(); };
      const td4b = document.createElement('td'); const inp4b = document.createElement('input'); inp4b.type='number'; inp4b.step='1'; inp4b.min='0'; inp4b.className='form-control form-control-sm'; inp4b.value=it.meses ?? 0; inp4b.oninput=function(){ it.meses=Number(this.value)||0; updateTotals(); };
      const td5 = document.createElement('td'); const inp5 = document.createElement('input'); inp5.type='number'; inp5.step='0.01'; inp5.min='0'; inp5.className='form-control form-control-sm'; inp5.value=it.aliquota_percent ?? 0; inp5.oninput=function(){ it.aliquota_percent = Number(this.value)||0; updateTotals(); };
      const td6 = document.createElement('td'); const inp6 = document.createElement('input'); inp6.type='number'; inp6.step='0.01'; inp6.min='0'; inp6.className='form-control form-control-sm'; inp6.value=it.desconto_percent ?? 0; inp6.oninput=function(){ it.desconto_percent = Number(this.value)||0; updateTotals(); };
      const td7 = document.createElement('td'); td7.textContent='R$ '+fmt(calcItemTotal(it)); td7.dataset.idx=idx;
      const td8 = document.createElement('td'); const rem = document.createElement('button'); rem.type='button'; rem.className='btn btn-sm btn-outline-danger'; rem.textContent='Remover'; rem.onclick=function(){ ITEMS.splice(idx,1); render(); };
      td1.appendChild(inp1); td2.appendChild(inp2); td3.appendChild(inp3); td4.appendChild(inp4); td5.appendChild(inp5); td6.appendChild(inp6); td8.appendChild(rem);
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
      const m = Number($meses.value)||0;
      const vuBr = ($vu.value||'').trim();
      if(!d || !u || !q || !vuBr) return;
      if(!isValidMoneyBR(vuBr)) return;
      const vu = toDecBR(vuBr);
      ITEMS.push({descricao:d, unidade:u, quantidade:q, meses:m, valor_unitario:vu, valor_unitario_br:vuBr, aliquota_percent:0, desconto_percent:0});
      $desc.value=''; $unid.value=''; $qtd.value=''; $vu.value='';
      $meses.value='';
      render();
    });
  }

  render();

  if(form){
    form.addEventListener('submit', function(e){
      let ok = true;
      moneyInputs.forEach(inp=>{
        const val = inp.value;
        if(val){ if(!isValidMoneyBR(val)) ok=false; inp.value = String(val).replace(/\./g,'').replace(/,/g,'.'); }
      });
      try{
        const data = JSON.stringify(ITEMS.map(x=>({descricao:x.descricao, unidade:x.unidade, quantidade:x.quantidade, meses:x.meses, valor_unitario:x.valor_unitario, aliquota_percent:x.aliquota_percent, desconto_percent:x.desconto_percent})));
        const hidden = document.getElementById('itens_fornecimento_e');
        if(hidden) hidden.value = data;
      }catch(err){ ok=false; }
      if(!ok){ e.preventDefault(); alert('Verifique os campos: formatos inválidos encontrados.'); }
    });
  }
})();
</script>
@endsection
