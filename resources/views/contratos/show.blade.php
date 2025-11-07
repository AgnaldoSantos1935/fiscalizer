@extends('layouts.app')
@section('title', 'Detalhes do Contrato')

@section('content')
<div>

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
        </ol>
    </nav>

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
                     <a id="btnNovoEmpenho" href="#" class="btn btn-light btn-sm d-none">
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
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                      <input type="number" step="0.01" class="form-control" id="valorPagamento" required>
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
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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

    // ========== FUNÇÃO PARA RECARREGAR O CONTRATO ==========
    function carregarContrato() {
        fetch('{{ route("api.contratos.detalhes", [ $id]) }}')


            .then(resp => resp.json())
            .then(data => {
                atualizarResumo(data);
                preencherDetalhes(data);
                preencherItens(data);
                preencherEmpenhos(data);
                   atualizarCardDinamico('resumoGlobal', data.valor_global);
        atualizarCardDinamico('resumoEmpenhado', data.totais?.valor_empenhado || 0);
        atualizarCardDinamico('resumoPago', data.totais?.valor_pago || 0);
        atualizarCardDinamico('resumoSaldo', data.totais?.saldo || 0);
        inicializarTooltips();// Atualiza dados e tooltips
            })
            .catch(err => console.error('Erro ao carregar contrato:', err));
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
                    <td>R$ ${parseFloat(item.valor_total).toLocaleString('pt-BR', {minimumFractionDigits:2})}</td>
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
        document.querySelectorAll('.ver-item').forEach(btn=>{
            btn.addEventListener('click',e=>{
                const item=JSON.parse(e.currentTarget.getAttribute('data-item'));
                document.getElementById('conteudoItem').innerHTML=`
                    <table class="table table-bordered">
                        <tr><th>Descrição Completa</th><td>${item.descricao_item??'—'}</td></tr>
                        <tr><th>Unidade</th><td>${item.unidade_medida??'—'}</td></tr>
                        <tr><th>Quantidade</th><td>${item.quantidade??'—'}</td></tr>
                        <tr><th>Valor Unitário</th><td>R$ ${parseFloat(item.valor_unitario).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td></tr>
                        <tr><th>Valor Total</th><td>R$ ${parseFloat(item.valor_total).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td></tr>
                        <tr><th>Tipo</th><td>${item.tipo_item??'—'}</td></tr>
                        <tr><th>Justificativa</th><td>${item.justificativa??'—'}</td></tr>
                    </table>`;
               // new bootstrap.Modal(document.getElementById('modalDetalhesItem')).show();
                abrirModalItem('modalDetalhesItem');

            });
        });
    }

    // ========== 4️⃣ Tabela de Empenhos ==========
    function preencherEmpenhos(data){
        const emp=data.empenhos??[];
        if(!emp.length){
            document.getElementById('tabelaEmpenhos').innerHTML=`<p class="text-muted">Nenhum empenho vinculado a este contrato.</p>`;
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
                    <td>R$ ${parseFloat(e.valor).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td>
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
        document.querySelectorAll('.ver-empenho').forEach(btn=>{
            btn.addEventListener('click',e=>{
                const emp=JSON.parse(e.currentTarget.getAttribute('data-empenho'));
                document.getElementById('conteudoEmpenho').innerHTML=`
                    <table class="table table-bordered">
                        <tr><th>Número</th><td>${emp.numero??'—'}</td></tr>
                        <tr><th>Data</th><td>${emp.data_empenho?new Date(emp.data_empenho).toLocaleDateString('pt-BR'):'—'}</td></tr>
                        <tr><th>Valor</th><td>R$ ${parseFloat(emp.valor).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td></tr>
                        <tr><th>Projeto/Atividade</th><td>${emp.projeto_atividade??'—'}</td></tr>
                        <tr><th>Fonte</th><td>${emp.fonte_recurso??'—'}</td></tr>
                        <tr><th>Elemento</th><td>${emp.elemento_despesa??'—'}</td></tr>
                        <tr><th>Observação</th><td>${emp.observacao??'—'}</td></tr>
                    </table>`;
                //new bootstrap.Modal(document.getElementById('modalDetalhesEmpenho')).show();
                abrirModalEmpenho('modalDetalhesEmpenho');

            });
        });
        document.querySelectorAll('.pagar-empenho').forEach(btn=>{
            btn.addEventListener('click',e=>{
                const id=e.currentTarget.dataset.empenhoId;
                const numero=e.currentTarget.dataset.empenhoNumero;
                document.getElementById('empenhoId').value=id;
                document.getElementById('empenhoNumero').value=numero;
                new bootstrap.Modal(document.getElementById('modalPagamento')).show();
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

});
</script>
@endsection
