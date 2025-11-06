/**
 * Fiscalizer – Utilitário de controle de modais (contratos)
 * Autor: ChatGPT (OpenAI)
 * Compatível com Bootstrap 5.3+
 */

document.addEventListener("DOMContentLoaded", function () {

  // 🔹 Reativa eventos Bootstrap (fechar modal, tooltips)
  function reativarBootstrapComponentes() {
    // Fechamento manual (caso data-bs-dismiss tenha sido recriado)
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
      btn.addEventListener('click', () => {
        const modalEl = btn.closest('.modal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
      });
    });

    // Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new bootstrap.Tooltip(el);
    });
  }

  // 🔹 Utilitário para formatar valores em BRL
  function formatarValor(valor) {
    return 'R$ ' + parseFloat(valor || 0).toLocaleString('pt-BR', {
      minimumFractionDigits: 2
    });
  }

  // ==========================================================
  // 🔸 Modal Detalhes do Item
  // ==========================================================
  window.abrirModalItem = function (item) {
    const modalEl = document.getElementById('modalDetalhesItem');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const corpo = modalEl.querySelector('#conteudoItem');

    corpo.innerHTML = `
      <table class="table table-striped align-middle">
        <tr><th>Descrição</th><td>${item.descricao_item ?? '—'}</td></tr>
        <tr><th>Unidade</th><td>${item.unidade_medida ?? '—'}</td></tr>
        <tr><th>Quantidade</th><td>${item.quantidade ?? '—'}</td></tr>
        <tr><th>Valor Unitário</th><td>${formatarValor(item.valor_unitario)}</td></tr>
        <tr><th>Valor Total</th><td>${formatarValor(item.valor_total)}</td></tr>
        <tr><th>Status</th><td>${item.status ?? '—'}</td></tr>
      </table>
    `;

    reativarBootstrapComponentes();
    modal.show();
  };

  // ==========================================================
  // 🔸 Modal Detalhes do Empenho
  // ==========================================================
  window.abrirModalEmpenho = function (empenho) {
    const modalEl = document.getElementById('modalDetalhesEmpenho');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const corpo = modalEl.querySelector('#conteudoEmpenho');

    corpo.innerHTML = `
      <table class="table table-striped align-middle">
        <tr><th>Número</th><td>${empenho.numero ?? '—'}</td></tr>
        <tr><th>Data</th><td>${empenho.data_empenho ? new Date(empenho.data_empenho).toLocaleDateString('pt-BR') : '—'}</td></tr>
        <tr><th>Valor</th><td>${formatarValor(empenho.valor)}</td></tr>
        <tr><th>Projeto Atividade</th><td>${empenho.projeto_atividade ?? '—'}</td></tr>
        <tr><th>Observação</th><td>${empenho.observacao ?? '—'}</td></tr>
      </table>
    `;

    reativarBootstrapComponentes();
    modal.show();
  };

  // ==========================================================
  // 🔸 Modal Novo Pagamento
  // ==========================================================
  window.abrirModalPagamento = function (empenhoId, numeroEmpenho) {
    const modalEl = document.getElementById('modalPagamento');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    // Preenche dados do empenho no formulário
    document.getElementById('empenhoId').value = empenhoId;
    document.getElementById('empenhoNumero').value = numeroEmpenho;

    reativarBootstrapComponentes();
    modal.show();
  };

  // 🔸 Salvar pagamento (exemplo simples via fetch API)
  const btnSalvar = document.getElementById('btnSalvarPagamento');
  if (btnSalvar) {
    btnSalvar.addEventListener('click', function () {
      const payload = {
        empenho_id: document.getElementById('empenhoId').value,
        valor_pagamento: document.getElementById('valorPagamento').value,
        data_pagamento: document.getElementById('dataPagamento').value,
        documento: document.getElementById('documentoPagamento').value,
        observacao: document.getElementById('obsPagamento').value
      };

      fetch('/api/pagamentos', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
      })
      .then(r => r.json())
      .then(resp => {
        alert('Pagamento registrado com sucesso!');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        document.getElementById('formPagamento').reset();
      })
      .catch(err => {
        console.error('Erro ao registrar pagamento:', err);
        alert('Erro ao salvar o pagamento.');
      });
    });
  }

  // Inicializa tooltips da página ao carregar
  reativarBootstrapComponentes();


});
