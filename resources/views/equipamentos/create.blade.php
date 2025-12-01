@extends('layouts.app')

@section('title', 'Cadastrar Equipamento')

@section('breadcrumb')
    @include('layouts.components.breadcrumbs', [
        'trail' => [
            ['label' => 'Equipamentos', 'icon' => 'fas fa-desktop', 'url' => route('equipamentos.index')],
            ['label' => 'Cadastrar Equipamento']
        ]
    ])
@endsection

@section('content_body')
<h2 class="mb-4">Cadastrar Equipamento</h2>
@if(session('import_result'))
<div class="card mb-3">
  <div class="card-body">
    <div class="mb-2">
      <strong>Resultado da importação:</strong>
      <div class="small text-muted">Registros criados: {{ session('import_result')['sucesso'] }} | Linhas com erro: {{ count(session('import_result')['erros'] ?? []) }}</div>
    </div>
    @if(count(session('import_result')['erros'] ?? []) > 0)
    <div class="table-responsive small">
      <table class="table table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th>Linha</th>
            <th>Erros</th>
          </tr>
        </thead>
        <tbody>
        @foreach(session('import_result')['erros'] as $e)
          <tr>
            <td>{{ $e['linha'] }}</td>
            <td>{{ implode('; ', $e['mensagens'] ?? []) }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
  </div>
@endif

<form action="{{ route('equipamentos.store') }}" method="POST" class="card p-4 shadow-sm">
    @csrf

    <h5 class="mt-0">Origem do Inventário</h5>
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Origem *</label>
            <select name="origem_inventario" class="form-select" required>
                <option value="">Selecione...</option>
                <option value="manual">Manual</option>
                <option value="agente">Agente (Automático)</option>
                <option value="importacao">Importação CSV</option>
            </select>
        </div>
    </div>

    <div id="secOrigemAgente" class="alert alert-info small" style="display:none;">
        Para origem "Agente", alguns campos (SO, RAM, CPU) podem ser preenchidos automaticamente após o primeiro check-in.
    </div>
    <div id="secOrigemImportacao" class="alert alert-warning small" style="display:none;">
        Origem "Importação CSV" é destinada ao cadastro em massa. Utilize esta origem apenas se estiver replicando dados de uma planilha.
    </div>

    <div id="secOrigemImportUpload" class="card p-3 mb-3" style="display:none;">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Arquivo CSV</label>
                <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv" form="uploadCsvForm">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" type="submit" form="uploadCsvForm">Enviar CSV</button>
            </div>
        </div>
    </div>

    <h5 class="mt-3">Localização (DRE/Município/Unidade)</h5>
    <input type="hidden" name="unidade_id" id="unidadeIdHidden" required>
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">DRE *</label>
            <select id="selectDre" class="form-select" required>
                <option value="">Selecione...</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Município *</label>
            <select id="selectMunicipio" class="form-select" required disabled>
                <option value="">Selecione...</option>
            </select>
            <div class="form-text" id="countMunicipios"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Unidade *</label>
            <input type="text" id="filterUnidade" class="form-control form-control-sm mb-1" placeholder="Pesquisar unidade..." disabled>
            <select id="selectUnidade" class="form-select" required disabled>
                <option value="">Selecione...</option>
            </select>
            <div class="form-text" id="countUnidades"></div>
        </div>
    </div>
    <div id="unidadeResolved" class="alert alert-secondary small" style="display:none;">
        Unidade selecionada: <span id="unidadeResolvedNome"></span>
    </div>
    <div id="unidadeFallback" class="row mb-3" style="display:none;">
        <div class="col-md-6">
            <label class="form-label">Unidade (fallback) *</label>
            <select id="unidadeFallbackSelect" class="form-select">
                <option value="">-- Selecione --</option>
                @foreach($unidades as $u)
                    <option value="{{ $u->id }}">{{ $u->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- 🔹 Identificação -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Hostname *</label>
            <input type="text" name="hostname" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Serial Number *</label>
            <input type="text" name="serial_number" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Tipo *</label>
            <select id="tipoEquip" name="tipo" class="form-select" required>
                <option value="">Selecione...</option>
                <option value="desktop">Desktop</option>
                <option value="notebook">Notebook</option>
                <option value="servidor">Servidor</option>
                <option value="switch">Switch</option>
                <option value="roteador">Roteador</option>
                <option value="outro">Outro</option>
            </select>
        </div>
    </div>

    <!-- 🔹 Sistema e Hardware -->
    <h5 class="mt-3">Configurações de Hardware</h5>
    <div id="secHardware" class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Sistema Operacional *</label>
            <input id="inputSO" type="text" name="sistema_operacional" class="form-control" required>
        </div>

        <div class="col-md-2">
            <label class="form-label">RAM (GB)</label>
            <input type="number" name="ram_gb" class="form-control" min="1">
        </div>

        <div class="col-md-3">
            <label class="form-label">CPU Resumida</label>
            <input type="text" name="cpu_resumida" class="form-control" placeholder="Ex: Intel i5-8400">
        </div>

        <div class="col-md-3">
            <label class="form-label">Discos</label>
            <input type="text" name="discos" class="form-control" placeholder="Ex: SSD 240GB">
        </div>
    </div>

    <!-- 🔹 Rede -->
    <h5 class="mt-3">Rede</h5>
    <div id="secRede" class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">IP Atual</label>
            <input type="text" name="ip_atual" class="form-control" placeholder="Ex: 192.168.0.10">
        </div>

        <div class="col-md-4">
            <label class="form-label">Último Check-in</label>
            <input type="datetime-local" name="ultimo_checkin" class="form-control">
        </div>
    </div>


    <div id="secExtrasSwitch" class="row mb-3" style="display:none;">
        <div class="col-md-3">
            <label class="form-label">Portas</label>
            <input id="spec_portas" type="number" min="1" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Gerenciável</label>
            <select id="spec_gerenciavel" class="form-select">
                <option value="">—</option>
                <option value="sim">Sim</option>
                <option value="nao">Não</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Fabricante</label>
            <input id="spec_fabricante" type="text" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Modelo</label>
            <input id="spec_modelo" type="text" class="form-control">
        </div>
    </div>

    <div id="secExtrasRoteador" class="row mb-3" style="display:none;">
        <div class="col-md-3">
            <label class="form-label">Firmware</label>
            <input id="spec_firmware" type="text" class="form-control" placeholder="Ex: RouterOS 7.8">
        </div>
        <div class="col-md-3">
            <label class="form-label">WAN</label>
            <select id="spec_wan_tipo" class="form-select">
                <option value="">—</option>
                <option value="pppoe">PPPoE</option>
                <option value="ip_fixo">IP Fixo</option>
                <option value="dhcp">DHCP</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">LAN Ports</label>
            <input id="spec_lan_ports" type="number" min="1" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">VPN</label>
            <select id="spec_vpn" class="form-select">
                <option value="">—</option>
                <option value="sim">Sim</option>
                <option value="nao">Não</option>
            </select>
        </div>
    </div>

    <div id="secExtrasOutro" class="row mb-3" style="display:none;">
        <div class="col-md-12">
            <label class="form-label">Descrição</label>
            <input id="spec_descricao" type="text" class="form-control" placeholder="Ex: Impressora térmica, Scanner, etc.">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Especificações Detalhadas</label>
        <textarea id="especificacoesTextarea" name="especificacoes" rows="4" class="form-control" placeholder=""></textarea>
    </div>

    <!-- 🔹 Ações -->
    <div class="text-end">
        <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Cadastrar Equipamento</button>
    </div>

</form>
<form id="uploadCsvForm" action="{{ route('equipamentos.import') }}" method="POST" enctype="multipart/form-data" class="d-none">
    @csrf
</form>
@endsection

@section('css')
@endsection

@section('js')
<script>
(function(){
  const $tipo = document.getElementById('tipoEquip');
  const $secHW = document.getElementById('secHardware');
  const $secRede = document.getElementById('secRede');
  const $so = document.getElementById('inputSO');
  const $specSwitch = document.getElementById('secExtrasSwitch');
  const $specRouter = document.getElementById('secExtrasRoteador');
  const $specOutro = document.getElementById('secExtrasOutro');
  const $form = document.querySelector('form[action*="equipamentos"]');
  const $textSpecs = document.getElementById('especificacoesTextarea');
  const $origem = document.querySelector('select[name="origem_inventario"]');
  const $infoAgente = document.getElementById('secOrigemAgente');
  const $infoImport = document.getElementById('secOrigemImportacao');
  const $uploadForm = document.getElementById('secOrigemImportUpload');
  const $selDre = document.getElementById('selectDre');
  const $selMunicipio = document.getElementById('selectMunicipio');
  const $selUnidade = document.getElementById('selectUnidade');
  const $countMuns = document.getElementById('countMunicipios');
  const $countUnids = document.getElementById('countUnidades');
  const $filterUnidade = document.getElementById('filterUnidade');
  let LAST_UNIDADES = [];
  const $unidHidden = document.getElementById('unidadeIdHidden');
  const $unidResolved = document.getElementById('unidadeResolved');
  const $unidResolvedNome = document.getElementById('unidadeResolvedNome');
  const $unidFallback = document.getElementById('unidadeFallback');
  const $unidFallbackSelect = document.getElementById('unidadeFallbackSelect');
  const $ip = document.querySelector('input[name="ip_atual"]');
  const $serial = document.querySelector('input[name="serial_number"]');
  const $hostname = document.querySelector('input[name="hostname"]');
  const $ram = document.querySelector('input[name="ram_gb"]');
  const $lanPorts = document.getElementById('spec_lan_ports');
  const ESCOLAS = @json($escolasArr);
  const DRES = @json($dresArr);
  const UNIDADES = @json($unidadesArr);

  function setDefaultSO(tipo){
    if (!tipo) return;
    if (tipo === 'switch') $so.value = $so.value || 'Embedded OS';
    else if (tipo === 'roteador') $so.value = $so.value || 'RouterOS 7.8';
    else if (tipo === 'outro') $so.value = $so.value || '—';
  }

  function update(){
    const t = ($tipo.value||'').toLowerCase();
    const isCompute = ['desktop','notebook','servidor'].includes(t);
    $secHW.style.display = '';
    setDefaultSO(t);
    $specSwitch.style.display = (t==='switch') ? '' : 'none';
    $specRouter.style.display = (t==='roteador') ? '' : 'none';
    $specOutro.style.display = (t==='outro') ? '' : 'none';
    applyDynamicRequired(isCompute);
  }

  function buildSpecs(){
    const t = ($tipo.value||'').toLowerCase();
    const obj = {};
    if (t==='switch'){
      obj.portas = valNum('spec_portas');
      obj.gerenciavel = valStr('spec_gerenciavel');
      obj.fabricante = valStr('spec_fabricante');
      obj.modelo = valStr('spec_modelo');
    } else if (t==='roteador'){
      obj.firmware = valStr('spec_firmware');
      obj.wan_tipo = valStr('spec_wan_tipo');
      obj.lan_ports = valNum('spec_lan_ports');
      obj.vpn = valStr('spec_vpn');
    } else if (t==='outro'){
      obj.descricao = valStr('spec_descricao');
    }
    const txt = ($textSpecs.value||'').trim();
    const cleaned = Object.fromEntries(Object.entries(obj).filter(([,v])=>v!==null && v!=='' && v!==undefined));
    if (Object.keys(cleaned).length>0){
      try { $textSpecs.value = JSON.stringify(cleaned); } catch(e) {}
    } else {
      $textSpecs.value = txt;
    }
  }

  function valStr(id){ const el = document.getElementById(id); return el ? (el.value||'') : ''; }
  function valNum(id){ const el = document.getElementById(id); if (!el) return null; const v = parseInt(el.value,10); return isNaN(v)?null:v; }

  if ($tipo){
    $tipo.addEventListener('change', update);
    update();
  }
  if ($form){
    $form.addEventListener('submit', function(e){
      buildSpecs();
      if (!validateForm()) {
        e.preventDefault();
      }
    });
  }
  if ($origem){
    $origem.addEventListener('change', updateByOrigem);
    updateByOrigem();
  }

  if ($selDre){
    initLocalizacao();
    $selDre.addEventListener('change', onDreChange);
  }
  if ($selMunicipio){
    $selMunicipio.addEventListener('change', onMunicipioChange);
  }
  if ($selUnidade){
    $selUnidade.addEventListener('change', onUnidadeChange);
  }
  if ($unidFallbackSelect){
    $unidFallbackSelect.addEventListener('change', function(){
      const id = ($unidFallbackSelect.value||'').trim();
      $unidHidden.value = id;
      if (id){
        const u = UNIDADES.find(x=> String(x.id)===String(id));
        if (u){ $unidResolvedNome.textContent = u.nome; $unidResolved.style.display='block'; }
      }
    });
  }

  function applyDynamicRequired(isCompute){
    if (!$so) return;
    $so.required = !!isCompute;
    const lbl = $so.closest('.col-md-4')?.querySelector('.form-label');
    if (lbl) {
      const base = lbl.textContent.replace(/\s\*$/, '');
      lbl.textContent = base + (isCompute ? ' *' : '');
    }
    if (!isCompute) clearError($so);
  }

  function validateForm(){
    let ok = true;
    const isManual = (($origem?.value||'').toLowerCase()==='manual');
    const requiredSelectors = [
      'input[name="hostname"]',
      'input[name="serial_number"]',
      'select[name="tipo"]',
      'select[name="origem_inventario"]',
      ...(isManual ? ['#selectUnidade'] : [])
    ];
    requiredSelectors.forEach(sel => {
      const el = document.querySelector(sel);
      if (el && !valuePresent(el)) { showError(el, 'Campo obrigatório'); ok = false; } else if (el) { clearError(el); }
    });
    if (isManual && !$unidHidden.value){ showError($selUnidade, 'Selecione a Unidade'); ok = false; }
    const t = ($tipo.value||'').toLowerCase();
    const isCompute = ['desktop','notebook','servidor'].includes(t);
    if (isCompute && $so && !valuePresent($so)) { showError($so, 'Informe o sistema operacional'); ok = false; } else if ($so) { clearError($so); }
    if ($ip && valuePresent($ip) && !validIP($ip.value)) { showError($ip, 'IP inválido'); ok = false; } else if ($ip) { clearError($ip); }
    if ($serial) { $serial.value = ($serial.value||'').toUpperCase().trim(); }
    if ($hostname) { $hostname.value = ($hostname.value||'').trim(); }
    return ok;
  }

  function valuePresent(el){
    const v = (el.value||'').trim();
    return v !== '';
  }

  function showError(el, msg){
    el.classList.add('is-invalid');
    let fb = el.nextElementSibling;
    if (!fb || !fb.classList.contains('invalid-feedback')){
      fb = document.createElement('div');
      fb.className = 'invalid-feedback';
      el.parentNode.insertBefore(fb, el.nextSibling);
    }
    fb.textContent = msg;
  }

  function clearError(el){
    el.classList.remove('is-invalid');
    const fb = el.nextElementSibling;
    if (fb && fb.classList.contains('invalid-feedback')){
      fb.textContent = '';
    }
  }

  ['input[name="hostname"]','input[name="serial_number"]','select[name="origem_inventario"]','#selectUnidade','select[name="tipo"]','#inputSO','#selectDre','#selectMunicipio']
    .forEach(sel => {
      const el = document.querySelector(sel);
      if (el) el.addEventListener('input', ()=> clearError(el));
      if (el) el.addEventListener('change', ()=> clearError(el));
    });

  function updateByOrigem(){
    const o = ($origem?.value||'').toLowerCase();
    if ($infoAgente) $infoAgente.style.display = (o==='agente') ? 'block' : 'none';
    if ($infoImport) $infoImport.style.display = (o==='importacao') ? 'block' : 'none';
    if ($uploadForm) $uploadForm.style.display = (o==='importacao') ? 'block' : 'none';
    if ($so) $so.placeholder = (o==='agente') ? 'Preenchido pelo Agente após check-in' : '';
    if ($selDre){ $selDre.disabled = false; }
    if ($selMunicipio){ $selMunicipio.disabled = ($selMunicipio.options.length === 0); }
    if ($selUnidade){ $selUnidade.disabled = ($selUnidade.options.length === 0); }
    $unidHidden.value='';
    $unidResolved.style.display='none';
    $unidFallback.style.display='none';
    if ($selDre && $selDre.options.length === 0) { initLocalizacao(); }
  }

  function initLocalizacao(){
    const dres = (DRES||[]).map(d=>({value:d.codigo,label:d.nome}));
    if (dres.length > 0){
      fillSelectObj($selDre, dres);
      $selMunicipio.disabled = true;
      $selUnidade.disabled = true;
      if ($countMuns) $countMuns.textContent = '';
      if ($countUnids) $countUnids.textContent = '';
      if ($filterUnidade) { $filterUnidade.value=''; $filterUnidade.disabled = true; }
    } else {
      if ($selDre){ $selDre.innerHTML = '<option value="">(sem DRE)</option>'; $selDre.disabled = true; }
      const municipios = Array.from(new Set(ESCOLAS.map(e=>e.municipio).filter(Boolean))).sort((a,b)=>String(a).localeCompare(String(b)));
      $selMunicipio.disabled = false;
      fillSelect($selMunicipio, municipios);
      $selUnidade.disabled = true;
      fillSelectObj($selUnidade, []);
      if ($countMuns) $countMuns.textContent = `${municipios.length} municípios`;
      if ($countUnids) $countUnids.textContent = '';
      if ($filterUnidade) { $filterUnidade.value=''; $filterUnidade.disabled = true; }
    }
  }
  function onDreChange(){
    $unidHidden.value='';
    $unidResolved.style.display='none';
    $unidFallback.style.display='none';
    const dreCodigo = ($selDre.value||'');
    if (!dreCodigo){
      $selMunicipio.disabled = true;
      fillSelect($selMunicipio, []);
      $selUnidade.disabled = true;
      fillSelectObj($selUnidade, []);
      return;
    }
    $selMunicipio.disabled = false;
    const municipios = Array.from(new Set(ESCOLAS.filter(e=>e.dre===dreCodigo).map(e=>e.municipio).filter(Boolean))).sort((a,b)=>String(a).localeCompare(String(b)));
    fillSelect($selMunicipio, municipios);
    $selUnidade.disabled = true;
    fillSelectObj($selUnidade, []);
    if ($countMuns) $countMuns.textContent = `${municipios.length} municípios`;
    if ($countUnids) $countUnids.textContent = '';
    if ($filterUnidade) { $filterUnidade.value=''; $filterUnidade.disabled = true; }
    if (municipios.length === 1){ $selMunicipio.value = municipios[0]; onMunicipioChange(); }
  }
  function onMunicipioChange(){
    $unidHidden.value='';
    $unidResolved.style.display='none';
    $unidFallback.style.display='none';
    const dre = ($selDre.value||'');
    const mun = ($selMunicipio.value||'');
    if (!mun){
      $selUnidade.disabled = true;
      fillSelectObj($selUnidade, []);
      return;
    }
    const escolaNomes = ESCOLAS.filter(e=> (dre ? e.dre===dre : true) && e.municipio===mun).map(e=>String(e.nome||'')).filter(Boolean);
    const unidades = UNIDADES.filter(u=> escolaNomes.some(n=> normalize(n)===normalize(u.nome))).sort((a,b)=>String(a.nome).localeCompare(String(b.nome)));
    const items = unidades.map(u=>({value:String(u.id), label:String(u.nome)}));
    $selUnidade.disabled = false;
    fillSelectObj($selUnidade, items);
    LAST_UNIDADES = items.slice();
    if ($countUnids) $countUnids.textContent = `${items.length} unidades`;
    if ($filterUnidade) { $filterUnidade.disabled = items.length>0 ? false : true; $filterUnidade.value=''; }
    if (items.length === 1){ $selUnidade.value = items[0].value; onUnidadeChange(); }
  }
  function onUnidadeChange(){
    $unidHidden.value='';
    $unidResolved.style.display='none';
    $unidFallback.style.display='none';
    const id = ($selUnidade.value||'');
    if (id){
      const u = UNIDADES.find(x=> String(x.id)===String(id));
      if (u){
        $unidHidden.value = u.id;
        $unidResolvedNome.textContent = u.nome;
        $unidResolved.style.display='block';
      }
    }
  }
  function fillSelect(sel, items){
    if (!sel) return;
    sel.innerHTML = '<option value="">Selecione...</option>' + items.map(v=>`<option value="${String(v)}">${String(v)}</option>`).join('');
    sel.value = '';
  }
  function fillSelectObj(sel, items){
    if (!sel) return;
    sel.innerHTML = '<option value="">Selecione...</option>' + items.map(o=>`<option value="${String(o.value)}">${String(o.label)}</option>`).join('');
    sel.value = '';
  }
  function normalize(s){ return String(s||'').trim().toLowerCase(); }
  function validIP(v){
    const parts = String(v||'').trim().split('.');
    if (parts.length !== 4) return false;
    for (let p of parts){ const n = Number(p); if (!/^[0-9]{1,3}$/.test(p)) return false; if (n<0 || n>255) return false; }
    return true;
  }
  function digitsOnly(v){ return String(v||'').replace(/\D/g,''); }
  function normalizeHostname(v){
    let s = String(v||'').toLowerCase();
    s = s.replace(/[^a-z0-9.-]/g,'-');
    s = s.replace(/-+/g,'-');
    s = s.replace(/(^-|-$)/g,'');
    return s;
  }

  if ($ip){
    $ip.addEventListener('blur', function(){ if (this.value && !validIP(this.value)) showError(this,'IP inválido'); else clearError(this); });
    $ip.addEventListener('input', function(){ clearError(this); });
  }
  if ($serial){
    $serial.addEventListener('input', function(){ this.value = (this.value||'').toUpperCase(); clearError(this); });
  }
  if ($hostname){
    $hostname.addEventListener('input', function(){ this.value = normalizeHostname(this.value); clearError(this); });
  }
  if ($ram){
    $ram.addEventListener('input', function(){ this.value = digitsOnly(this.value); clearError(this); });
    $ram.addEventListener('blur', function(){ const n = Number(this.value); if (this.value && (!Number.isInteger(n) || n<=0)) showError(this,'Informe apenas números (GB)'); else clearError(this); });
  }
  if ($lanPorts){
    $lanPorts.addEventListener('input', function(){ this.value = digitsOnly(this.value); clearError(this); });
    $lanPorts.addEventListener('blur', function(){ const n = Number(this.value); if (this.value && (!Number.isInteger(n) || n<=0)) showError(this,'Informe apenas números'); else clearError(this); });
  }

  if ($filterUnidade){
    $filterUnidade.addEventListener('input', function(){
      const q = normalize(this.value);
      const list = (LAST_UNIDADES||[]).filter(o=> normalize(o.label).includes(q));
      fillSelectObj($selUnidade, list);
      $selUnidade.disabled = list.length===0;
      if ($countUnids) $countUnids.textContent = `${list.length} unidades`;
      if (list.length === 1){ $selUnidade.value = list[0].value; onUnidadeChange(); }
    });
  }
})();
</script>
@endsection
