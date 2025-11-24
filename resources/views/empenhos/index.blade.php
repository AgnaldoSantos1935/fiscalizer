@extends('layouts.app')
@section('title', 'Notas de Empenho')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <!-- 🔹 Filtros -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
      <h4 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
      </h4>
    </div>
    <div class="card-body bg-white">
      <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small">Número</label>
          <input type="text" id="filtroNumero" class="form-control form-control-sm" placeholder="Ex: 2023/0001">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small">Empresa</label>
          <input type="text" id="filtroEmpresa" class="form-control form-control-sm" placeholder="Razão Social">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small">Contrato</label>
          <input type="text" id="filtroContrato" class="form-control form-control-sm" placeholder="Número do contrato">
        </div>
        <div class="col-md-3 d-flex justify-content-end align-items-end">
          <div class="d-flex w-100">
            <button type="button" id="btnAplicarFiltros" class="btn btn-primary btn-sm btn-sep flex-grow-1">
              <i class="fas fa-filter me-1"></i> Filtrar
            </button>
            <button type="button" id="btnLimpar" class="btn btn-outline-secondary btn-sm btn-sep flex-grow-1">
              <i class="fas fa-undo me-1"></i> Limpar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- 🔹 Lista -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Notas de Empenho</h4>
      <a href="{{ route('empenhos.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Novo Empenho</a>
    </div>

    <div class="card-body">
      <!-- 🔹 Workflow Mensal (Jan–Dez) -->
      <div class="mb-3" id="workflowMeses">
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <span class="text-secondary small me-2">Workflow mensal:</span>
          <!-- badges gerados via JS -->
        </div>
      </div>
      <!-- 🔹 Modal Upload PDF Emitido -->
      <div class="modal fade" id="modalUploadEmitido" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content rounded-4">
            <div class="modal-header">
              <h5 class="modal-title">Enviar PDF de Empenho Emitido</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="formUploadEmitido">
                <div class="mb-3">
                  <label class="form-label">Arquivo PDF</label>
                  <input type="file" name="pdf" accept="application/pdf" class="form-control" required>
                </div>
                <input type="hidden" name="empenho_id" id="upload_empenho_id">
                <input type="hidden" name="mes" id="upload_mes">
                <input type="hidden" name="ano" id="upload_ano">
              </form>
              <div class="small text-secondary">O workflow "Emitido" será concluído após o upload do PDF.</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" id="btnConfirmUploadEmitido" class="btn btn-primary">Enviar PDF</button>
            </div>
          </div>
        </div>
      </div>
      <!-- 🔹 Navbar de ações -->
      <nav class="nav nav-pills mb-3">
        <ul class="nav nav-pills">
          <li class="nav-item">
            <a id="navDetalhes" class="nav-link disabled" href="#">
              <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
            </a>
          </li>
        </ul>
      </nav>

      <table id="tabelaEmpenhos" class="table table-striped no-inner-borders w-100">
        <thead>
          <tr>
            <th class="text-center" style="width: 45px;"></th>
            <th>Número</th>
            <th>Empresa</th>
            <th>Contrato</th>
            <th>Data</th>
            <th class="text-end">Valor Total</th>
            <th>Workflow</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection

@section('css')
@endsection

@section('js')
<script>
$(function() {
  // ===== Util: último dia de um mês do ano (mes: 1–12)
  function ultimoDiaDoMes(ano, mes) {
    return new Date(ano, mes, 0); // Date usa monthIndex (0–11), passando mes (1–12) com dia 0 → último dia
  }
  function mesHabilitado(ano, mes) {
    const hoje = new Date();
    const ultimoDia = ultimoDiaDoMes(ano, mes);
    const diaSeguinte = new Date(ultimoDia.getFullYear(), ultimoDia.getMonth(), ultimoDia.getDate() + 1);
    return hoje >= diaSeguinte;
  }
  function nomeMesPt(mIndex) {
    const nomes = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
    return nomes[mIndex];
  }

  // ===== Filtro por mês ativo (1–12) via DataTables custom search
  let mesFiltroAtivo = null;
  $.fn.dataTable.ext.search.push(function(settings, data) {
    if (settings.nTable.id !== 'tabelaEmpenhos') return true;
    if (!mesFiltroAtivo) return true;
    // "Data" coluna índice 4 (dd/mm/aaaa)
    const dataStr = data[4] || '';
    const partes = String(dataStr).split('/');
    const mesLinha = parseInt(partes[1] || '0', 10);
    return mesLinha === mesFiltroAtivo;
  });

  // ===== Renderiza badges dos meses (Jan–Dez)
  (function renderBadgesMeses(){
    const cont = document.querySelector('#workflowMeses .d-flex');
    const anoAtual = new Date().getFullYear();
    for (let mIndex = 0; mIndex < 12; mIndex++) {
      const mes = mIndex + 1; // 1–12
      const habilitado = mesHabilitado(anoAtual, mes);
      const classe = habilitado ? 'bg-success' : 'bg-danger';
      const badge = document.createElement('a');
      badge.className = `badge rounded-pill ${classe} text-decoration-none`;
      badge.setAttribute('href', '#');
      badge.setAttribute('data-mes', mes.toString());
      if (!habilitado) badge.setAttribute('aria-disabled', 'true');
      badge.innerText = nomeMesPt(mIndex);
      badge.title = habilitado ? 'Habilitado para ações' : 'Aguardando fim do mês';
      badge.addEventListener('click', function(e){
        e.preventDefault();
        mesFiltroAtivo = mes;
        $('#tabelaEmpenhos').DataTable().draw();
      });
      cont.appendChild(badge);
    }
  })();

  const tabela = $('#tabelaEmpenhos').DataTable({
    ajax: `{{ route('empenhos.data') }}`,
    language: { url: '{{ asset("js/pt-BR.json") }}' },
    pageLength: 10,
    order: [[1, 'asc']],
    dom: 't<"bottom"p>',
    responsive: true,
    columns: [
      {
        data: 'id', orderable: false, className: 'text-center',
        render: (id) => `<input type="radio" name="empenhoSelecionado" value="${id}">`
      },
      { data: 'numero', name: 'numero' },
      { data: 'empresa', name: 'empresa.razao_social' },
      { data: 'contrato', name: 'contrato.numero' },
      { data: 'data_lancamento', name: 'data_lancamento' },
      { data: 'valor_total', name: 'valor_total', className: 'text-end fw-semibold' },
      {
        data: null,
        name: 'workflow',
        render: function(row){
          // Extrai mês e ano da data (formato dd/mm/aaaa)
          const partes = String(row.data_lancamento || '').split('/');
          const mes = parseInt(partes[1] || String(new Date().getMonth()+1), 10);
          const ano = parseInt(partes[2] || String(new Date().getFullYear()), 10);
          const habilitado = mesHabilitado(ano, mes);
          const clsOn = 'badge rounded-pill bg-success';
          const clsOff = 'badge rounded-pill bg-danger';
          const attrOff = 'aria-disabled="true" style="pointer-events:none;opacity:0.85;"';
          const solicitadoClassBase = `${habilitado?clsOn:clsOff}`;
          const solicitadoClass = row.solicitado_at ? 'badge rounded-pill bg-secondary' : solicitadoClassBase;
          const solicitadoDisabled = row.solicitado_at ? 'style="pointer-events:none;opacity:0.9;"' : (habilitado?'':attrOff);
          const solicitado = `<a href="#" class="${solicitadoClass}" ${solicitadoDisabled} title="Solicitado" data-stage="solicitado" data-id="${row.id}" data-mes="${mes}" data-ano="${ano}">Solicitado</a>`;
          const emitidoClassBase = `${habilitado?clsOn:clsOff} ms-1`;
          const emitidoClass = row.emitido_at ? 'badge rounded-pill bg-secondary ms-1' : emitidoClassBase;
          const emitidoDisabled = row.emitido_at ? 'style="pointer-events:none;opacity:0.9;"' : (habilitado?'':attrOff);
          const emitido = `<a href="#" class="${emitidoClass}" ${emitidoDisabled} title="Emitido" data-stage="emitido" data-id="${row.id}" data-mes="${mes}" data-ano="${ano}">Emitido</a>`;
          const pago = `<a href="#" class="${habilitado?clsOn:clsOff} ms-1" ${habilitado?'':attrOff} title="Pago" data-stage="pago" data-id="${row.id}" data-mes="${mes}" data-ano="${ano}">Pago</a>`;
          return `${solicitado} ${emitido} ${pago}`;
        }
      }
    ]
  });

  let empenhoSelecionado = null;
  $('#tabelaEmpenhos').on('change', 'input[name="empenhoSelecionado"]', function () {
    empenhoSelecionado = $(this).val();
    $('#navDetalhes').removeClass('disabled');
  });
  $('#navDetalhes').on('click', function (e) {
    e.preventDefault();
    if (!empenhoSelecionado) return;
    window.location.href = '{{ url('empenhos') }}' + '/' + empenhoSelecionado;
  });

  $('#btnAplicarFiltros').on('click', function () {
    tabela.column(1).search($('#filtroNumero').val());
    tabela.column(2).search($('#filtroEmpresa').val());
    tabela.column(3).search($('#filtroContrato').val());
    tabela.draw();
  });

  $('#btnLimpar').on('click', function () {
    $('#formFiltros')[0].reset();
    tabela.search('').columns().search('');
    tabela.order([1, 'asc']);
    tabela.ajax.reload(null, false);
    $('#navDetalhes').addClass('disabled');
    empenhoSelecionado = null;
  });

  // Clique em "Solicitado": gera PDF de Pretensão e persiste status + notifica gestor
  $('#tabelaEmpenhos').on('click', '.badge[data-stage="solicitado"]', function(e){
    e.preventDefault();
    const id = $(this).data('id');
    const mes = $(this).data('mes');
    const ano = $(this).data('ano');
    const url = `{{ route('empenhos.pretensao_pdf', ':id') }}`.replace(':id', id) + `?mes=${mes}&ano=${ano}`;
    window.open(url, '_blank');
    const persistUrl = `{{ route('empenhos.pretensao_solicitar', ':id') }}`.replace(':id', id);
    const badge = $(this);
    $.ajax({
      url: persistUrl,
      method: 'POST',
      data: { mes, ano },
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(resp){
        badge
          .removeClass('bg-success bg-danger')
          .addClass('bg-secondary')
          .attr('title', 'Solicitação concluída, registrada e enviada ao gestor')
          .css({ pointerEvents: 'none', opacity: 0.9 });
      },
      error: function(xhr){
        alert('Falha ao registrar solicitação: ' + (xhr.responseJSON?.message || 'erro desconhecido'));
      }
    });
  });

  // Clique em "Emitido": abre modal para upload do PDF, conclui etapa após envio
  let emitidoTargetBadge = null;
  const modalUpload = new bootstrap.Modal(document.getElementById('modalUploadEmitido'));
  $('#tabelaEmpenhos').on('click', '.badge[data-stage="emitido"]', function(e){
    e.preventDefault();
    const id = $(this).data('id');
    const mes = $(this).data('mes');
    const ano = $(this).data('ano');
    $('#upload_empenho_id').val(id);
    $('#upload_mes').val(mes);
    $('#upload_ano').val(ano);
    emitidoTargetBadge = $(this);
    modalUpload.show();
  });

  $('#btnConfirmUploadEmitido').on('click', function(){
    const id = $('#upload_empenho_id').val();
    const form = document.getElementById('formUploadEmitido');
    const fd = new FormData(form);
    const url = `{{ route('empenhos.emitido_upload', ':id') }}`.replace(':id', id);
    $.ajax({
      url: url,
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(resp){
        modalUpload.hide();
        if (emitidoTargetBadge) {
          emitidoTargetBadge
            .removeClass('bg-success bg-danger')
            .addClass('bg-secondary')
            .attr('title', 'Emitido: PDF enviado, registrado e etapa concluída')
            .css({ pointerEvents: 'none', opacity: 0.9 });
        }
        // opcional: feedback
        alert('PDF enviado com sucesso. Etapa Emitido concluída.');
      },
      error: function(xhr){
        alert('Falha no upload do PDF: ' + (xhr.responseJSON?.message || 'erro desconhecido'));
      }
    });
  });
});
</script>
@endsection
