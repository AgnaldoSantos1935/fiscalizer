/**
 * Fiscalizer – Utilitário de controle de modais (contratos)
 * Compatível com Bootstrap 5.3+ e integração via Vite
 */

export function inicializarModaisContratos() {
    document.addEventListener("DOMContentLoaded", function () {

        // Reativa eventos Bootstrap (fechar modal, tooltips)
        function reativarBootstrapComponentes() {
            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const modalEl = btn.closest('.modal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                });
            });

            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
        }

        function formatarValor(valor) {
            return 'R$ ' + parseFloat(valor || 0).toLocaleString('pt-BR', {
                minimumFractionDigits: 2
            });
        }

        // Modal Detalhes do Item
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

        // Modal Detalhes do Empenho
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

        // Modal Novo Pagamento
        window.abrirModalPagamento = function (empenhoId, numeroEmpenho) {
            const modalEl = document.getElementById('modalPagamento');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            document.getElementById('empenhoId').value = empenhoId;
            document.getElementById('empenhoNumero').value = numeroEmpenho;

            reativarBootstrapComponentes();
            modal.show();
        };

        // Salvar pagamento
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
                .then(() => {
                    Fiscalizer.showToast('success', 'Pagamento registrado com sucesso!');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    document.getElementById('formPagamento').reset();
                })
                .catch(err => {
                    console.error('Erro ao registrar pagamento:', err);
                    Fiscalizer.showToast('error', 'Erro ao salvar o pagamento.');
                });
            });
        }

        reativarBootstrapComponentes();
    });
}
