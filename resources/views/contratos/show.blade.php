@extends('layouts.app')
@section('title', 'Detalhes do Contrato')

@section('content')
@include('layouts.components.breadcrumbs')
<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 🔹 Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
            <li class="breadcrumb-item">
                <a href="{{ route('contratos.index') }}" class="text-decoration-none text-primary fw-semibold">
                    <i class="fas fa-file-contract me-1"></i> Contratos
                </a>
            </li>
            <li class="breadcrumb-item active text-secondary fw-semibold" id="breadcrumbContrato">
                Carregando...
            </li>
            @if(request()->get('from') === 'empenhos.create')
                <li class="breadcrumb-item">
                    <span class="text-secondary">Empenhos</span>
                </li>
                <li class="breadcrumb-item active text-secondary fw-semibold">
                    Novo Empenho
                </li>
            @endif
        </ol>
    </nav>
    <!-- 🔹 Alerta de erro de carregamento -->
    <div id="alertErroContrato" class="mb-3"></div>

    <!-- 🔹 Nova Vigência (se houver aditivo de prazo) -->
    <div id="alertNovaVigencia" class="mb-3"></div>

    

 <!-- 🔹 Resumo Financeiro -->
  <div class="card card">
              <div class="card-header">
                <div class="card-title">
                 <h5 class="mb-0 text-with" id="tituloContrato"></h5>
                     </div>
 <div class="card-tools">
                 
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div><!-- /.card-header -->
              <div class="card-body">
 <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
                 <div class="small-box bg-success"  data-bs-toggle="tooltip" title="Valor global do contrato por ano">
              <div class="inner">
              <h4 id="resumoGlobal">R$ 0,00</h4>
               <p></p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">Valor Global por ano</a>
            </div><!---->
          </div>
            <div class="col-lg-3 col-6">
                             <div class="small-box bg-success"  data-bs-toggle="tooltip" title="Valor total empenhado até o momento">
              <div class="inner">
                <h4 id="resumoEmpenhado">R$ 0,00</h4>
                  <p></p>

              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer"> Valores Empenhados</a>
            </div><!---->
          </div>
            <div class="col-lg-3 col-6">
                  <div class="small-box bg-warning"  data-bs-toggle="tooltip" title="Total da soma dos pagamentos informados até o momento">
              <div class="inner">
                <h4 id="resumoPago">R$ 0,00</h4>
                <p></p>

              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">Valores Pagos</i></a>
            </div><!---->
          </div>
               <div class="col-lg-3 col-6">
                <div class="small-box bg-danger" data-bs-toggle="tooltip" title="Saldo da somas dos valores empenhados">
              <div class="inner">
                 <h4 id="resumoSaldo">R$ 0,00</h4>
                <p></p>

              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">Saldo Disponível</i></a>
            </div><!---->
          </div>
          </div>
          </div>
</section>
    <!-- final do Resumo Financeiro -->
  </div>
</div>

    <!-- 🔹 Vigência do Contrato (dias restantes) -->
    <div class="card card mb-3">
      <div class="card-header">
        <div class="card-title">
          <h5 class="mb-0 text-with">Vigência do Contrato</h5>
        </div>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>
        </div>
      </div>
      <div class="card-body" id="cardContadorVigencia">
        <p class="text-muted mb-0">Calculando dias de vigência...</p>
      </div>
    </div>

    <!-- 🔹 Detalhes do Contrato -->
<div class="card card">
              <div class="card-header">
                <div class="card-title">
                 <h5 class="mb-0 text-with">Detalhes do contrato: </h5>
                     </div>
 <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
        </div><!-- /.card-header -->

        <div class="card-body" id="detalhesContrato">
            <p class="text-muted mb-0">Carregando informações do contrato...</p>
    </div>
    </div>

    <!-- 🔹 Documentos Relacionados -->
    <div class="card card">
      <div class="card-header">
        <div class="card-title">
          <h5 class="mb-0 text-with">Documentos Relacionados</h5>
        </div>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>
          <a id="btnNovoDocumento" href="{{ route('contratos.documentos.create', $id) }}" class="btn btn-outline-info btn-sm">
            <i class="fas fa-file-upload"></i> Inserir Documento
          </a>
        </div>
      </div>
      <div class="card-body" id="tabelaDocumentosRelacionados">
        <p class="text-muted mb-0">Carregando documentos relacionados...</p>
      </div>
    </div>

    <!-- 🔹 Itens Contratados -->
              <div class="card card">
              <div class="card-header">
                <div class="card-title">
                 <h5 class="mb-0 text-with">Itens Contratados: </h5>
                     </div>
 <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div><!-- /.card-header -->
        <div class="card-body" id="tabelaItens">
            <p class="text-muted mb-0">Carregando itens...</p>
        </div>
  </div>

    <!-- 🔹 Empenhos Vinculados -->

              <div class="card card">
              <div class="card-header">
                <div class="card-title">
                 <h5 class="mb-0 text-with"> Empenhos Vinculados: </h5>
                     </div>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                     <a id="btnNovoEmpenho" href="{{ route('empenhos.create') }}?contrato_id={{ $id }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Cadastrar Empenho
            </a>
                </div>
        </div><!-- /.card-header -->
        <div class="card-body" id="tabelaEmpenhos">
            <p class="text-muted mb-0">Carregando empenhos...</p>
        </div>
  </div>


<!-- 🔹 Modal Detalhes do Item -->
<div class="modal fade" id="modalDetalhesItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title"><i class="fas fa-box me-2"></i>Detalhes do Item</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="conteudoItem">
              <p class="text-muted">Carregando...</p>
          </div>
      </div>
  </div>
</div>

<!-- 🔹 Modal Detalhes do Empenho -->
<div class="modal fade" id="modalDetalhesEmpenho" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2"></i>Detalhes do Empenho</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="conteudoEmpenho">
              <p class="text-muted">Carregando...</p>
          </div>
      </div>
  </div>
</div>

<!-- 🔹 Modal Novo Pagamento -->
<div class="modal fade" id="modalPagamento" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header bg-success text-white">
              <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i>Novo Pagamento</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <form id="formPagamento">
                  <input type="hidden" id="empenhoId">
                  <div class="mb-3">
                      <label class="form-label">Empenho</label>
                      <input type="text" id="empenhoNumero" class="form-control" readonly>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Valor do Pagamento (R$)</label>
                      <input type="text" class="form-control money-br-input" id="valorPagamento" required>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Data do Pagamento</label>
                      <input type="date" class="form-control" id="dataPagamento">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Documento/OB</label>
                      <input type="text" class="form-control" id="documentoPagamento">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Observação</label>
                      <textarea class="form-control" id="obsPagamento" rows="2"></textarea>
                  </div>
              </form>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-success" id="btnSalvarPagamento">Salvar</button>
          </div>
      </div>
  </div>
  </div>
</div>
@endsection
@section( section:'css')
<style>
.tooltip-inner {
  background-color: rgba(0, 0, 0, 0.85);
  font-size: 0.85rem;
}
</style>

@endsection

@section('js')

<script>
document.addEventListener("DOMContentLoaded", function() {
    const idContrato = window.location.pathname.split('/').pop();

    // =========================
    // Utilidades e Modais (inline)
    // =========================
    function formatarValor(valor) {
      return 'R$ ' + parseFloat(valor || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
    }

    function reativarBootstrapComponentes() {
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        if (window.bootstrap && bootstrap.Tooltip) {
          new bootstrap.Tooltip(el);
        }
      });
    }

    // Exibe um modal com compatibilidade BS5/BS4
    function showModal(modalEl) {
      try {
        if (window.bootstrap && typeof bootstrap.Modal === 'function') {
          let instance = null;
          if (typeof bootstrap.Modal.getInstance === 'function') {
            instance = bootstrap.Modal.getInstance(modalEl);
          }
          if (!instance) {
            instance = new bootstrap.Modal(modalEl);
          }
          instance.show();
        } else if (typeof $ !== 'undefined' && typeof $(modalEl).modal === 'function') {
          $(modalEl).modal('show');
        } else {
          // Fallback simples
          modalEl.classList.add('show');
          modalEl.style.display = 'block';
          modalEl.setAttribute('aria-modal', 'true');
          modalEl.removeAttribute('aria-hidden');
        }
      } catch (e) {
        console.error('Erro ao abrir modal:', e);
      }
    }

    // Fecha um modal com compatibilidade BS5/BS4
    function hideModal(modalEl) {
      try {
        if (window.bootstrap && typeof bootstrap.Modal === 'function') {
          let instance = null;
          if (typeof bootstrap.Modal.getInstance === 'function') {
            instance = bootstrap.Modal.getInstance(modalEl);
          }
          if (!instance) {
            instance = new bootstrap.Modal(modalEl);
          }
          instance.hide();
        } else if (typeof $ !== 'undefined' && typeof $(modalEl).modal === 'function') {
          $(modalEl).modal('hide');
        } else {
          // Fallback simples
          modalEl.classList.remove('show');
          modalEl.style.display = 'none';
          modalEl.setAttribute('aria-hidden', 'true');
          modalEl.removeAttribute('aria-modal');
        }
      } catch (e) {
        console.error('Erro ao fechar modal:', e);
      }
    }

    // Delegação para botões de fechar (compatível com BS4 e BS5)
    document.addEventListener('click', function(evt) {
      const trigger = evt.target.closest('[data-bs-dismiss="modal"], [data-dismiss="modal"]');
      if (!trigger) return;
      const modalEl = trigger.closest('.modal');
      if (modalEl) {
        // Se Bootstrap já estiver gerenciando, não interfere
        if (!(window.bootstrap && typeof bootstrap.Modal === 'function')) {
          evt.preventDefault();
          hideModal(modalEl);
        }
      }
    });

    function normalizarLabel(chave) {
      return (chave || '')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase());
    }

    function formatarCampo(chave, valor) {
      if (valor === null || valor === undefined || valor === '') return '—';
      const num = Number(valor);
      if (/valor|total|preco|unitario|montante/i.test(chave) && !isNaN(num)) {
        return formatarValor(num);
      }
      if (/data/i.test(chave)) {
        try {
          const d = new Date(valor);
          if (!isNaN(d.getTime())) return d.toLocaleDateString('pt-BR');
        } catch {}
      }
      return String(valor);
    }

    function isForeignKeyKey(k) {
      return /^id$/i.test(k) || /_id$/i.test(k) || /(Id|ID)$/.test(k);
    }

    function isTimestampKey(k) {
      return (
        /^(created|updated|deleted)_?at$/i.test(k) ||
        /^(createdAt|updatedAt|deletedAt)$/i.test(k) ||
        /^timestamps?$/i.test(k)
      );
    }

    function renderTabelaDetalhes(obj, destaque = []) {
      if (!obj || typeof obj !== 'object') return '<p class="text-muted">Sem dados.</p>';

      const keys = Object.keys(obj).filter(k => !isForeignKeyKey(k) && !isTimestampKey(k));

      const isPrimitive = v => (v === null || v === undefined) ? true : (typeof v !== 'object');
      const primaries = keys.filter(k => isPrimitive(obj[k]));
      const orderedPrimaries = [
        ...destaque.filter(k => primaries.includes(k)),
        ...primaries.filter(k => !destaque.includes(k))
      ];

      let linhas = '';
      orderedPrimaries.forEach(k => {
        linhas += `<tr><th>${normalizarLabel(k)}</th><td>${formatarCampo(k, obj[k])}</td></tr>`;
      });

      // Arrays: mostra o tamanho ou lista simples se forem primitivos
      const arrays = keys.filter(k => Array.isArray(obj[k]));
      arrays.forEach(k => {
        const arr = obj[k] || [];
        if (!arr.length) return;
        const allPrimitive = arr.every(v => isPrimitive(v));
        if (allPrimitive) {
          const preview = arr.map(v => formatarCampo(k, v)).join(', ');
          linhas += `<tr><th>${normalizarLabel(k)}</th><td>${preview}</td></tr>`;
        } else {
          linhas += `<tr><th>${normalizarLabel(k)}</th><td>${arr.length} registro(s)</td></tr>`;
        }
      });

      // Objetos relacionados: imprime campos internos (um nível), filtrando FKs
      const nested = keys.filter(k => obj[k] && typeof obj[k] === 'object' && !Array.isArray(obj[k]));
      nested.forEach(k => {
        const child = obj[k];
        const childKeys = Object.keys(child).filter(ck => !isForeignKeyKey(ck) && !isTimestampKey(ck) && isPrimitive(child[ck]));
        if (!childKeys.length) return;
        linhas += `<tr class="table-light"><th colspan="2">${normalizarLabel(k)}</th></tr>`;
        childKeys.forEach(ck => {
          linhas += `<tr><td class="fw-semibold">${normalizarLabel(ck)}</td><td>${formatarCampo(ck, child[ck])}</td></tr>`;
        });
      });

      return `<table class="table table-striped align-middle">${linhas}</table>`;
    }

    window.abrirModalItem = function(item) {
      const modalEl = document.getElementById('modalDetalhesItem');
      const corpo = modalEl.querySelector('#conteudoItem');
      corpo.innerHTML = renderTabelaDetalhes(item, [
        'descricao_item','unidade_medida','quantidade','valor_unitario','valor_total','tipo_item','justificativa'
      ]);
      reativarBootstrapComponentes();
      showModal(modalEl);
    };

    window.abrirModalEmpenho = function(emp) {
      const modalEl = document.getElementById('modalDetalhesEmpenho');
      const corpo = modalEl.querySelector('#conteudoEmpenho');
      corpo.innerHTML = renderTabelaDetalhes(emp, [
        'numero','data_empenho','valor','projeto_atividade','fonte_recurso','elemento_despesa','observacao'
      ]);
      reativarBootstrapComponentes();
      showModal(modalEl);
    };

    window.abrirModalPagamento = function(empenhoId, numeroEmpenho) {
      const modalEl = document.getElementById('modalPagamento');
      document.getElementById('empenhoId').value = empenhoId;
      document.getElementById('empenhoNumero').value = numeroEmpenho;
      reativarBootstrapComponentes();
      showModal(modalEl);
    };

    // ========== FUNÇÃO PARA RECARREGAR O CONTRATO ==========
    function carregarContrato() {
        const url = '{{ route("api.contratos.detalhes", [ $id]) }}';
        fetch(url)
            .then(resp => {
                const ct = resp.headers.get('content-type') || '';
                if (!resp.ok) {
                    // Converte texto para diagnóstico
                    return resp.text().then(txt => { throw new Error(`HTTP ${resp.status}: ${txt.slice(0,200)}`); });
                }
                if (ct.includes('application/json')) {
                    return resp.json();
                }
                // Quando a API retorna HTML (login/erro), gera mensagem amigável
                return resp.text().then(html => {
                    const isHtml = /<!DOCTYPE|<html/i.test(html);
                    if (isHtml) {
                        throw new Error('Sessão expirada ou sem permissão para acessar os detalhes do contrato.');
                    }
                    throw new Error('Resposta inesperada do servidor (não-JSON).');
                });
            })
            .then(data => {
                // Limpa alerta de erro quando dados chegam
                const alertErro = document.getElementById('alertErroContrato');
                if (alertErro) alertErro.innerHTML = '';
                atualizarResumo(data);
                preencherDetalhes(data);
                preencherItens(data);
                preencherEmpenhos(data);
                preencherDocumentosRelacionados(data);
                atualizarNovaVigencia(data);
                atualizarContadorVigencia(data);
                   atualizarCardDinamico('resumoGlobal', data.valor_global);
        atualizarCardDinamico('resumoEmpenhado', data.totais?.valor_empenhado || 0);
        atualizarCardDinamico('resumoPago', data.totais?.valor_pago || 0);
        atualizarCardDinamico('resumoSaldo', data.totais?.saldo || 0);
        inicializarTooltips();// Atualiza dados e tooltips
            })
            .catch(err => {
                console.error('Erro ao carregar contrato:', err);
                const alertErro = document.getElementById('alertErroContrato');
                if (alertErro) {
                    alertErro.innerHTML = `
                      <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                          ${err.message || 'Falha ao carregar detalhes do contrato.'}
                        </div>
                      </div>`;
                }
                // Coloca mensagens informativas nas seções
                const setMsg = (id, msg) => {
                    const el = document.getElementById(id);
                    if (el) el.innerHTML = `<p class="text-muted mb-0">${msg}</p>`;
                };
                setMsg('detalhesContrato', 'Não foi possível carregar os detalhes do contrato.');
                setMsg('tabelaItens', 'Não foi possível carregar os itens do contrato.');
                setMsg('tabelaEmpenhos', 'Não foi possível carregar os empenhos vinculados.');
                setMsg('tabelaDocumentosRelacionados', 'Não foi possível carregar os documentos relacionados.');
            });
    }

// Inicializa ou reativa tooltips
function inicializarTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
}


    // ========== 1️⃣ Atualiza resumo financeiro ==========
    function atualizarResumo(data) {
        const t = data.totais;
        document.getElementById('resumoGlobal').innerText = 'R$ ' + parseFloat(data.valor_global).toLocaleString('pt-BR', {minimumFractionDigits:2});
        document.getElementById('resumoEmpenhado').innerText = 'R$ ' + parseFloat(t.valor_empenhado).toLocaleString('pt-BR', {minimumFractionDigits:2});
        document.getElementById('resumoPago').innerText = 'R$ ' + parseFloat(t.valor_pago).toLocaleString('pt-BR', {minimumFractionDigits:2});
        document.getElementById('resumoSaldo').innerText = 'R$ ' + parseFloat(t.saldo).toLocaleString('pt-BR', {minimumFractionDigits:2});
    }

    // ========== 2️⃣ Preenche informações gerais ==========
    function preencherDetalhes(data) {
        document.getElementById('tituloContrato').innerHTML = `<i class="fas fa-file-contract me-2"></i>Contrato nº ${data.numero}`;
        document.getElementById('breadcrumbContrato').innerText = `Contrato nº ${data.numero}`;

        const situacao = data.situacao_contrato?.nome ?? 'Indefinida';
        const cor = data.situacao_contrato?.cor ?? 'secondary';
        const badge = `<span class="badge bg-${cor}">${situacao}</span>`;

        document.getElementById('detalhesContrato').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Empresa:</strong> ${data.contratada?.razao_social ?? '—'}</p>
                    <p><strong>CNPJ:</strong> ${data.contratada?.cnpj ?? '—'}</p>
                    <p><strong>Objeto:</strong> ${data.objeto ?? '—'}</p>
                    <p><strong>Vigência em meses:</strong> ${data.vigencia_meses ?? '—'}</p>
                     <p><strong>Processo de contratação:</strong> ${data.num_processo}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Valor Global:</strong> R$ ${parseFloat(data.valor_global).toLocaleString('pt-BR', {minimumFractionDigits:2})}</p>
                    <p><strong>Período:</strong>
                        ${data.data_inicio ? new Date(data.data_inicio).toLocaleDateString('pt-BR') : '—'}
                        a
                        ${data.data_fim ? new Date(data.data_fim).toLocaleDateString('pt-BR') : '—'}
                    </p>
                    <p><strong>Situação:</strong> ${badge}</p>
                    <p><strong>Final da vigência:</strong> ${formatarDataBR(data.data_final) ?? '—'}</p>
                    <p><strong>Modalidade de Licitação:</strong> ${data.modalidade}</p>

                </div>
            </div>
        `;
        // Removido: ações de "Cadastrar Documento" e "Ver PDF do Contrato" na tela de detalhes
    }

    // ========== 3️⃣ Tabela de Itens ==========
    function preencherItens(data) {
        const itens = data.itens ?? [];
        if (!itens.length) {
            document.getElementById('tabelaItens').innerHTML = `<p class="text-muted">Nenhum item vinculado a este contrato.</p>`;
            return;
        }
        let linhas = '';
        itens.forEach((item, i) => {
            linhas += `
                <tr>
                    <td>${i+1}</td>
                    <td class="text-truncate" style="max-width:250px">${item.descricao_item ?? '—'}</td>
                    <td>${item.quantidade ?? '—'}</td>
                    <td data-format="currency" data-value="${item.valor_total}"></td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm ver-item"
                                data-item='${JSON.stringify(item).replace(/'/g, "&apos;")}'>
                            <i class="fas fa-search"></i>
                        </button>
                    </td>
                </tr>`;
        });
        document.getElementById('tabelaItens').innerHTML = `
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr><th>#</th><th>Descrição</th><th>Quantidade</th><th>Valor Total (R$)</th><th>Ações</th></tr>
                </thead><tbody>${linhas}</tbody>
            </table>`;
        document.querySelectorAll('.ver-item').forEach(btn => {
            btn.addEventListener('click', e => {
                const item = JSON.parse(e.currentTarget.getAttribute('data-item'));
                // Usa o módulo de modais para preencher e abrir
                abrirModalItem(item);
            });
        });
    }

    // ========== 4️⃣ Tabela de Empenhos ==========
    function preencherEmpenhos(data){
        const emp=data.empenhos??[];
        if(!emp.length){
            document.getElementById('tabelaEmpenhos').innerHTML=`
                <p class="text-muted">Nenhum empenho vinculado a este contrato.</p>`;
            return;
        }
        let linhas='';
        emp.forEach((e,i)=>{
            const dataEmp=e.data_empenho?new Date(e.data_empenho).toLocaleDateString('pt-BR'):'—';
            linhas+=`
                <tr>
                    <td>${i+1}</td>
                    <td>${e.numero??'—'}</td>
                    <td>${dataEmp}</td>
                    <td data-format="currency" data-value="${e.valor}"></td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm ver-empenho" data-empenho='${JSON.stringify(e).replace(/'/g,"&apos;")}'>
                            <i class="fas fa-search"></i>
                        </button>
                        <button class="btn btn-outline-success btn-sm pagar-empenho" data-empenho-id="${e.id}" data-empenho-numero="${e.numero}">
                            <i class="fas fa-money-bill-wave"></i>
                        </button>
                    </td>
                </tr>`;
        });
        document.getElementById('tabelaEmpenhos').innerHTML=`
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr><th>#</th><th>Número</th><th>Data</th><th>Valor (R$)</th><th>Ações</th></tr>
                </thead><tbody>${linhas}</tbody>
            </table>`;
        document.querySelectorAll('.ver-empenho').forEach(btn => {
            btn.addEventListener('click', e => {
                const emp = JSON.parse(e.currentTarget.getAttribute('data-empenho'));
                // Usa o módulo de modais para preencher e abrir
                abrirModalEmpenho(emp);
            });
        });
        document.querySelectorAll('.pagar-empenho').forEach(btn => {
            btn.addEventListener('click', e => {
                const id = e.currentTarget.dataset.empenhoId;
                const numero = e.currentTarget.dataset.empenhoNumero;
                abrirModalPagamento(id, numero);
            });
        });
    }

    // ========== 5️⃣ Salvar Pagamento ==========
    document.getElementById('btnSalvarPagamento').addEventListener('click',()=>{
        const payload={
            empenho_id:document.getElementById('empenhoId').value,
            valor_pagamento:document.getElementById('valorPagamento').value,
            data_pagamento:document.getElementById('dataPagamento').value,
            documento:document.getElementById('documentoPagamento').value,
            observacao:document.getElementById('obsPagamento').value,
            _token:'{{ csrf_token() }}'
        };
        fetch('/api/pagamentos',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify(payload)
        })
        .then(resp=>resp.json())
        .then(()=>{
            bootstrap.Modal.getInstance
            (document.getElementById('modalPagamento')).show();
        }).then(resp => resp.json())
        .then(() => {

            // ✅ Fecha o modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalPagamento'));
            modal.hide();

            // ✅ Limpa o formulário
            document.getElementById('formPagamento').reset();

            // ✅ Mensagem de sucesso
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show mt-3';
            alert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                Pagamento registrado com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.getElementById('cardEmpenhos').prepend(alert);

            // ✅ Recarrega o contrato para atualizar saldos
            carregarContrato();
        })
        .catch(error => {
            console.error('Erro ao registrar pagamento:', error);
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show mt-3';
            alert.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                Erro ao registrar pagamento. Verifique os dados e tente novamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.getElementById('cardEmpenhos').prepend(alert);
        });
    });

    // Carrega o contrato ao abrir a página
    carregarContrato();

function atualizarCardDinamico(id, valor) {
    const elemento = document.getElementById(id);
    const box = elemento.closest('.small-box');
    const iconDiv = box.querySelector('.icon'); // onde o ícone será trocado

    const valorNumerico = parseFloat(valor || 0);
    elemento.innerText = 'R$ ' + valorNumerico.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

    // Remove classes antigas
    box.classList.remove('bg-success', 'bg-warning', 'bg-danger');

    // Define cor, tooltip e ícone conforme a faixa de valor
    let cor = 'bg-success';
    let tooltip = '🟢 Valor saudável — contrato com boa margem financeira';
    let icone = '<i class="fas fa-check-circle"></i>'; // ícone verde padrão

    if (valorNumerico <= 1000) {
        cor = 'bg-danger';
        tooltip = '🔴 Valor crítico — saldo muito baixo ou contrato com risco';
        icone = '<i class="fas fa-exclamation-triangle"></i>'; // alerta vermelho
    } else if (valorNumerico <= 50000) {
        cor = 'bg-warning';
        tooltip = '🟡 Valor moderado — atenção ao acompanhamento dos gastos';
        icone = '<i class="fas fa-exclamation-circle"></i>'; // atenção amarela
    }

    // Atualiza cor e tooltip
    box.classList.add(cor);
    box.setAttribute('data-bs-original-title', tooltip);

    // Atualiza ícone visual
    iconDiv.innerHTML = icone;
}
  //formatar data
  function formatarDataBR(dataISO) {
  if (!dataISO) return '—';
  try {
    const data = new Date(dataISO);
    if (isNaN(data)) return '—'; // evita erro se formato inválido
    return data.toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  } catch {
    return '—';
  }
}

    // Removida a listagem separada de termos aditivos; eles aparecem em Documentos Relacionados

    // ========== 6️⃣ Documentos Relacionados ==========
    function preencherDocumentosRelacionados(data) {
        const docs = data.documentos ?? [];
        const el = document.getElementById('tabelaDocumentosRelacionados');
        if (!el) return;
        if (!docs.length) {
            el.innerHTML = `<p class="text-muted mb-0">Nenhum documento relacionado a este contrato.</p>`;
            return;
        }
        let linhas = '';
        docs.forEach(d => {
            const tipoAmigavel = (d.documento_tipo && d.documento_tipo.nome) ? d.documento_tipo.nome : (d.tipo || '—');
            const titulo = d.titulo || '—';
            const dataUpload = d.data_upload ? new Date(d.data_upload).toLocaleDateString('pt-BR') : '—';
            const visualizarUrl = `{{ route('documentos.visualizar', '__ID__') }}`.replace('__ID__', d.id) + `?return_to=${encodeURIComponent(window.location.pathname)}`;
            linhas += `<tr>
                <td>${tipoAmigavel}</td>
                <td class="text-truncate" style="max-width:260px">${titulo}</td>
                <td>${dataUpload}</td>
                <td>
                    <a href="${visualizarUrl}" class="btn btn-outline-primary btn-sm" title="Visualizar documento">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                </td>
            </tr>`;
        });
        el.innerHTML = `
            <div class="table-responsive">
              <table class="table table-striped table-sm align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Tipo</th>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>${linhas}</tbody>
              </table>
            </div>`;
    }

    // ========== 7️⃣ Nova Vigência calculada ==========
    function atualizarNovaVigencia(data) {
        const baseFim = data.data_final ? new Date(data.data_final) : null;
        const aditivosPrazo = (data.documentos ?? []).filter(d => d.tipo === 'termo_aditivo' && d.nova_data_fim);
        let maxFim = baseFim;
        aditivosPrazo.forEach(d => {
            try {
                const nd = new Date(d.nova_data_fim);
                if (nd && !isNaN(nd) && (!maxFim || nd > maxFim)) {
                    maxFim = nd;
                }
            } catch {}
        });
        const alertEl = document.getElementById('alertNovaVigencia');
        if (maxFim && (!baseFim || maxFim > baseFim)) {
            alertEl.innerHTML = `
              <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fas fa-calendar-check me-2"></i>
                <div>
                  Nova vigência calculada até <strong>${maxFim.toLocaleDateString('pt-BR')}</strong> com base em termos aditivos.
                </div>
              </div>`;
        } else {
            alertEl.innerHTML = '';
        }
    }

    // ========== 8️⃣ Contador de dias para fim da vigência ==========
    function atualizarContadorVigencia(data) {
        const baseFim = data.data_final ? new Date(data.data_final) : null;
        const aditivosPrazo = (data.documentos ?? []).filter(d => d.tipo === 'termo_aditivo' && d.nova_data_fim);
        let fim = baseFim;
        aditivosPrazo.forEach(d => {
            try {
                const nd = new Date(d.nova_data_fim);
                if (nd && !isNaN(nd) && (!fim || nd > fim)) {
                    fim = nd;
                }
            } catch {}
        });
        if (!fim || isNaN(fim)) return;

        const agora = new Date();
        const diffMs = fim - agora;
        const diffDias = Math.max(0, Math.ceil(diffMs / (1000 * 60 * 60 * 24)));
        const mesesAprox = diffDias / 30.4375; // média dias/mês

        let badgeClass = 'bg-danger'; // vermelho padrão
        if (mesesAprox > 8) badgeClass = 'bg-primary'; // azul
        else if (mesesAprox > 5) badgeClass = 'bg-success'; // verde
        else if (mesesAprox > 4) badgeClass = 'bg-warning'; // amarelo
        else badgeClass = 'bg-danger'; // vermelho

        const container = document.getElementById('cardContadorVigencia');
        if (!container) return;
        const html = `
            <div id="contadorVigencia" class="mt-2">
                <span class="badge ${badgeClass} p-2" style="font-size:1.25rem;">
                    ${diffDias} dias até o final da vigência
                </span>
            </div>`;
        container.innerHTML = html;
    }

});
</script>
@endsection
