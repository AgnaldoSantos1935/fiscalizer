// ==========================================================
// 🌐 Fiscalizer – Controle de modais de Hosts (Conexões)
// ==========================================================

// Função principal: abre o modal com detalhes da conexão
import * as bootstrap from "bootstrap"; // ✅ Importa o módulo JS do Bootstrap

export function abrirModalHost(idHost) {
    const modalEl = document.getElementById('modalDetalhesConexao');
    if (!modalEl) {
        console.error('❌ Modal #modalDetalhesConexao não encontrado.');
        return;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const corpo = modalEl.querySelector('.modal-body');
    corpo.innerHTML = `<p class="text-muted">Carregando...</p>`;

    fetch(`/api/hosts/${idHost}`)
    .then(resp => {
        if (!resp.ok) throw new Error(`Erro HTTP ${resp.status}`);
        return resp.json();
    })
    .then(data => {
        // exibir dados normalmente
         corpo.innerHTML = `
                <table class="table table-striped align-middle">
                  <tr><th>Nome da Conexão</th><td>${data.nome_conexao ?? '—'}</td></tr>
                  <tr><th>Descrição</th><td>${data.descricao ?? '—'}</td></tr>
                  <tr><th>Provedor</th><td>${data.provedor ?? '—'}</td></tr>
                  <tr><th>Tecnologia</th><td>${data.tecnologia ?? '—'}</td></tr>
                  <tr><th>IP</th><td>${data.ip_atingivel ?? '—'}</td></tr>
                  <tr><th>Porta</th><td>${data.porta ?? '—'}</td></tr>
                  <tr><th>Status</th><td>${data.status ?? '—'}</td></tr>
                  <tr><th>Escola</th><td>${data.escola?.escola ?? '—'}</td></tr>
                  <tr><th>Município</th><td>${data.escola?.municipio ?? '—'}</td></tr>
                  <tr><th>Atualizado em</th><td>${data.updated_at ? new Date(data.updated_at).toLocaleString('pt-BR') : '—'}</td></tr>
                </table>
            `;
            modal.show();
    })
    .catch(err => {
        console.error('Erro ao carregar detalhes do host:', err);
        corpo.innerHTML = `<div class="alert alert-danger">
            Não foi possível carregar os detalhes da conexão.<br>
            Código do erro: ${err.message}
        </div>`;
    });

}

// Inicializa os eventos que acionam o modal
export function inicializarModaisHosts() {
    console.log('⚙️ Módulo de modais de Hosts inicializado');

    // Evento de clique no botão "Exibir Detalhes"
    $(document).on('click', '#navDetalhes', function (e) {
        e.preventDefault();
        const idHost = window.conexaoSelecionada;
        if (!idHost) {
            alert('Selecione uma conexão primeiro!');
            return;
        }
        abrirModalHost(idHost);
    });
}
