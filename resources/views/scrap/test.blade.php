@extends('layouts.app')

@section('title','Testando Scrap')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-cloud-download-alt text-primary me-2"></i>
        Testando Scrap
      </h5>
      <button id="btnStart" class="btn btn-primary btn-sm">
        <i class="fas fa-play me-1"></i> Start
      </button>
      <button id="btnStartSwagger" class="btn btn-outline-primary btn-sm ms-2">
        <i class="fas fa-code me-1"></i> Start Swagger
      </button>
    </div>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Origem</label>
        <div class="d-flex gap-3">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="origin" id="originUrl" value="url" checked>
            <label class="form-check-label" for="originUrl">Página/URL</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="origin" id="originFile" value="file">
            <label class="form-check-label" for="originFile">Arquivo local</label>
          </div>
        </div>
        <div class="mt-2">
          <label class="form-label">Fonte predefinida</label>
          <select id="presetSource" class="form-select">
            <option value="">— Selecionar —</option>
            <option value="https://www.sistemas.pa.gov.br/portaltransparencia/transparencia-tematica/educacao">Portal Transparência (Educação - PA)</option>
            <option value="https://raw.githubusercontent.com/plotly/datasets/master/2014_usa_states.csv">Plotly (Estados USA)</option>
          </select>
        </div>
        <div class="mt-2" id="urlBlock">
          <label class="form-label">URL da página ou CSV</label>
          <input id="csvUrl" type="text" class="form-control" value="https://www.sistemas.pa.gov.br/portaltransparencia/transparencia-tematica/educacao">
          <small class="text-muted">Aceita link direto de CSV ou página com botão “Exportar dados”.</small>
        </div>
        <div class="mt-2 d-none" id="fileBlock">
          <label class="form-label">Selecione o arquivo CSV</label>
          <input id="csvFile" type="file" accept=".csv,text/csv" class="form-control">
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="processServer">
            <label class="form-check-label" for="processServer">Processar no servidor (upload)</label>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label">Delimitador</label>
          <select id="csvDelimiter" class="form-select">
            <option value="," selected>Vírgula (,)</option>
            <option value=";">Ponto e vírgula (;)</option>
            <option value="tab">Tabulação (\t)</option>
            <option value="custom">Custom</option>
          </select>
          <input id="csvDelimiterCustom" type="text" class="form-control mt-2 d-none" placeholder="Delimitador custom (1 caractere)">
        </div>
        <div class="col-md-4">
          <label class="form-label">Cabeçalho</label>
          <select id="csvHeader" class="form-select">
            <option value="true" selected>Primeira linha é cabeçalho</option>
            <option value="false">Sem cabeçalho</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Itens por página</label>
          <input id="perPage" type="number" class="form-control" value="50" min="1" max="500">
        </div>
      </div>

      <div class="d-flex align-items-center gap-2 mb-2">
        <button id="btnPrev" class="btn btn-outline-secondary btn-sm" disabled><i class="fas fa-chevron-left"></i></button>
        <span class="small" id="pageInfo">Página 1</span>
        <button id="btnNext" class="btn btn-outline-secondary btn-sm" disabled><i class="fas fa-chevron-right"></i></button>
        <button id="btnForce" class="btn btn-outline-warning btn-sm ms-auto"><i class="fas fa-sync"></i> Forçar atualização</button>
        <button id="btnExportJson" class="btn btn-outline-success btn-sm"><i class="fas fa-download"></i> Exportar JSON</button>
        <input id="exportName" type="text" class="form-control form-control-sm ms-2" style="max-width: 220px" value="scrap_page.json" placeholder="Nome do arquivo">
      </div>

      <div class="progress mb-2 d-none" id="scrapProgressContainer">
        <div class="progress-bar progress-bar-striped progress-bar-animated" id="scrapProgressBar" role="progressbar" style="width: 0%">0%</div>
      </div>

      <div class="mb-2">
        <strong>Resultado JSON:</strong>
      </div>
      <pre id="jsonOutput" class="bg-light p-3 rounded" style="max-height: 360px; overflow: auto;">Clique em Start para carregar…</pre>
      <div class="mt-3">
        <div class="fw-semibold">Validação</div>
        <div class="small text-muted">Linhas com quantidade de colunas divergente</div>
        <ul id="issuesList" class="list-group list-group-flush"></ul>
      </div>
      <hr>
      <div class="mt-3">
        <div class="fw-semibold">Swagger (OpenAPI)</div>
        <div class="small text-muted">Endpoints detectados</div>
        <div class="row g-2 mb-2">
          <div class="col-md-3">
            <label class="form-label">Método</label>
            <select id="swaggerMethodFilter" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="GET">GET</option>
              <option value="POST">POST</option>
              <option value="PUT">PUT</option>
              <option value="DELETE">DELETE</option>
              <option value="PATCH">PATCH</option>
              <option value="OPTIONS">OPTIONS</option>
              <option value="HEAD">HEAD</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tag</label>
            <input id="swaggerTagFilter" type="text" class="form-control form-control-sm" placeholder="ex: educacao">
          </div>
          <div class="col-md-5">
            <label class="form-label">Buscar no caminho/summary</label>
            <div class="d-flex gap-2">
              <input id="swaggerSearch" type="text" class="form-control form-control-sm" placeholder="ex: /dados">
              <button id="swaggerReset" class="btn btn-outline-secondary btn-sm">Limpar</button>
            </div>
          </div>
        </div>
        <table class="table table-sm table-striped" id="swaggerTable">
          <thead>
            <tr>
              <th style="width:90px">Método</th>
              <th>Path</th>
              <th>Summary</th>
              <th>Tags</th>
            </tr>
          </thead>
          <tbody id="swaggerTbody"></tbody>
        </table>
        <pre id="swaggerOutput" class="bg-light p-3 rounded" style="max-height: 240px; overflow: auto;">Clique em Start Swagger…</pre>
      </div>
    </div>
  </div>
  <div id="alertArea"></div>
  </div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const btn = document.getElementById('btnStart');
  const btnPrev = document.getElementById('btnPrev');
  const btnNext = document.getElementById('btnNext');
  const btnForce = document.getElementById('btnForce');
  const urlInput = document.getElementById('csvUrl');
  const out = document.getElementById('jsonOutput');
  const swaggerOut = document.getElementById('swaggerOutput');
  const swaggerMethodFilter = document.getElementById('swaggerMethodFilter');
  const swaggerTagFilter = document.getElementById('swaggerTagFilter');
  const swaggerSearch = document.getElementById('swaggerSearch');
  const swaggerReset = document.getElementById('swaggerReset');
  const swaggerTbody = document.getElementById('swaggerTbody');
  let lastSwagger = null;
  const delimiterSel = document.getElementById('csvDelimiter');
  const delimiterCustomInput = document.getElementById('csvDelimiterCustom');
  const headerSel = document.getElementById('csvHeader');
  const perPageInput = document.getElementById('perPage');
  const issuesList = document.getElementById('issuesList');
  const pageInfo = document.getElementById('pageInfo');
  const originUrlRadio = document.getElementById('originUrl');
  const originFileRadio = document.getElementById('originFile');
  const urlBlock = document.getElementById('urlBlock');
  const fileBlock = document.getElementById('csvFile')?.closest('.mt-2');
  const fileInput = document.getElementById('csvFile');
  const btnExport = document.getElementById('btnExportJson');
  const presetSource = document.getElementById('presetSource');
  const exportNameInput = document.getElementById('exportName');
  const processServerCheck = document.getElementById('processServer');
  const pcont = document.getElementById('scrapProgressContainer');
  const pbar = document.getElementById('scrapProgressBar');
  let currentPage = 1;
  let lastResponse = null;
  window.CSRFToken = window.CSRFToken || (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

  function renderIssues(items){
    issuesList.innerHTML = (items || []).slice(0, 20).map(i => {
      return `<li class="list-group-item d-flex justify-content-between"><span>Linha ${i.line}</span><span class="badge bg-danger">${i.found_cols}/${i.expected_cols}</span></li>`;
    }).join('');
  }

  function refreshPager(){
    const total = lastResponse?.total || 0;
    const per = lastResponse?.per_page || Number(perPageInput.value || 50);
    const pages = lastResponse?.pages || Math.max(1, Math.ceil(total / per));
    pageInfo.textContent = `Página ${currentPage} de ${pages}`;
    btnPrev.disabled = currentPage <= 1;
    btnNext.disabled = currentPage >= pages;
  }

  function fetchPage(force=false){
    const useFile = originFileRadio.checked;
    const url = (urlInput.value || '').trim();
    const delimiter = delimiterSel.value || ',';
    const delimiter_custom = delimiterSel.value === 'custom' ? (delimiterCustomInput.value || '') : '';
    const has_header = headerSel.value === 'true';
    const per_page = Number(perPageInput.value || 50);
    if (!useFile && !url) { out.textContent = 'Informe a URL'; return; }
    out.textContent = 'Carregando…';
    pcont.classList.remove('d-none');
    pbar.style.width = '5%';
    pbar.textContent = 'Iniciando';
    const doRequest = (payload) => {
      pbar.style.width = '30%';
      pbar.textContent = 'Baixando';
      const isUpload = payload instanceof FormData;
      const headers = isUpload ? { 'X-CSRF-TOKEN': window.CSRFToken, 'Accept':'application/json' } : { 'Content-Type':'application/json', 'X-CSRF-TOKEN': window.CSRFToken, 'Accept':'application/json' };
      const body = isUpload ? payload : JSON.stringify(payload);
      fetch('{{ route('scrap.fetch') }}', { method: 'POST', headers, body })
        .then(r => r.json())
        .then(j => {
          pbar.style.width = '70%';
          pbar.textContent = 'Processando';
          lastResponse = j;
          out.textContent = JSON.stringify(j, null, 2);
          renderIssues(j.issues || []);
          refreshPager();
          pbar.style.width = '100%';
          pbar.textContent = 'Concluído';
          setTimeout(function(){ pcont.classList.add('d-none'); pbar.style.width='0%'; pbar.textContent='0%'; }, 400);
        })
        .catch(() => { out.textContent = 'Erro ao carregar.'; });
    };

    if (useFile) {
      const f = fileInput.files && fileInput.files[0];
      if (!f) { out.textContent = 'Selecione um arquivo'; return; }
      if (processServerCheck.checked) {
        pbar.style.width = '15%';
        pbar.textContent = 'Preparando upload';
        const fd = new FormData();
        fd.append('csv', f);
        fd.append('delimiter', delimiter);
        fd.append('delimiter_custom', delimiter_custom);
        fd.append('has_header', String(has_header));
        fd.append('page', String(currentPage));
        fd.append('per_page', String(per_page));
        fd.append('force', String(force));
        doRequest(fd);
      } else {
        pbar.style.width = '15%';
        pbar.textContent = 'Lendo arquivo';
        const reader = new FileReader();
        reader.onload = function(){
          const raw_csv = reader.result;
          doRequest({ raw_csv, delimiter, delimiter_custom, has_header, page: currentPage, per_page, force });
        };
        reader.onerror = function(){ out.textContent = 'Erro ao ler arquivo'; };
        reader.readAsText(f, 'utf-8');
      }
    } else {
      pbar.style.width = '15%';
      pbar.textContent = 'Preparando requisição';
      doRequest({ url, delimiter, delimiter_custom, has_header, page: currentPage, per_page, force });
    }
  }

  btn.addEventListener('click', function(){
    currentPage = 1;
    fetchPage(false);
  });

  btnPrev.addEventListener('click', function(){ if (currentPage > 1) { currentPage--; fetchPage(false); } });
  btnNext.addEventListener('click', function(){ currentPage++; fetchPage(false); });
  btnForce.addEventListener('click', function(){ fetchPage(true); });

  originUrlRadio.addEventListener('change', function(){ if (this.checked) { urlBlock.classList.remove('d-none'); document.getElementById('fileBlock').classList.add('d-none'); }});
  originFileRadio.addEventListener('change', function(){ if (this.checked) { urlBlock.classList.add('d-none'); document.getElementById('fileBlock').classList.remove('d-none'); }});
  delimiterSel.addEventListener('change', function(){ if (this.value === 'custom') { delimiterCustomInput.classList.remove('d-none'); } else { delimiterCustomInput.classList.add('d-none'); } });
  presetSource.addEventListener('change', function(){
    const v = this.value || '';
    if (!v) return;
    originUrlRadio.checked = true;
    urlBlock.classList.remove('d-none');
    document.getElementById('fileBlock').classList.add('d-none');
    urlInput.value = v;
  });
  btnExport.addEventListener('click', function(){
    if (!lastResponse) return;
    const blob = new Blob([JSON.stringify(lastResponse, null, 2)], { type: 'application/json' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = (exportNameInput.value || 'scrap_page.json');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  });

  document.getElementById('btnStartSwagger').addEventListener('click', function(){
    const url = (urlInput.value || '').trim();
    if (!url) { swaggerOut.textContent = 'Informe a URL'; return; }
    swaggerOut.textContent = 'Carregando Swagger…';
    fetch('{{ route('scrap.swagger') }}', {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': window.CSRFToken, 'Accept':'application/json' },
      body: JSON.stringify({ url })
    })
    .then(r => r.json())
    .then(j => {
      if (j.error) { swaggerOut.textContent = j.error; return; }
      lastSwagger = j;
      renderSwaggerTable();
      const ep = j.endpoints || [];
      swaggerOut.textContent = JSON.stringify({ info: j.info || {}, servers: j.servers || [], count: j.count || ep.length, endpoints: ep.slice(0, 200) }, null, 2);
    })
    .catch(() => { swaggerOut.textContent = 'Erro ao carregar Swagger.'; });
  });

  function renderSwaggerTable(){
    const items = (lastSwagger?.endpoints || []).slice();
    const m = (swaggerMethodFilter.value || '').toUpperCase();
    const t = (swaggerTagFilter.value || '').toLowerCase();
    const s = (swaggerSearch.value || '').toLowerCase();
    const filtered = items.filter(x => {
      if (m && x.method?.toUpperCase() !== m) return false;
      if (t) {
        const tags = (x.tags || []).map(y => String(y).toLowerCase());
        if (!tags.some(v => v.includes(t))) return false;
      }
      if (s) {
        const text = `${x.path || ''} ${(x.summary || '')}`.toLowerCase();
        if (!text.includes(s)) return false;
      }
      return true;
    }).slice(0, 300);
    swaggerTbody.innerHTML = filtered.map(x => {
      const tags = (x.tags || []).join(', ');
      return `<tr><td><span class="badge bg-dark">${x.method || ''}</span></td><td>${x.path || ''}</td><td>${x.summary || ''}</td><td>${tags}</td></tr>`;
    }).join('');
  }

  swaggerMethodFilter.addEventListener('change', renderSwaggerTable);
  swaggerTagFilter.addEventListener('input', renderSwaggerTable);
  swaggerSearch.addEventListener('input', renderSwaggerTable);
  swaggerReset.addEventListener('click', function(){ swaggerMethodFilter.value=''; swaggerTagFilter.value=''; swaggerSearch.value=''; renderSwaggerTable(); });
});
</script>
@endsection
