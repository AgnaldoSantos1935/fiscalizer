// ====================================================
// 🧠 fiscalizerAutocomplete.js — datalists (Municípios/Provedores)
// ====================================================

export function bootFiscalizerAutocomplete() {
  document.addEventListener('DOMContentLoaded', function () {
    // 🔹 IBGE — Municípios do Pará (UF 15)
    fetch('https://servicodados.ibge.gov.br/api/v1/localidades/estados/15/municipios')
      .then(resp => resp.json())
      .then(municipios => {
        const lista = document.getElementById('listaMunicipios');
        if (!lista) return;
        lista.innerHTML = '';
        municipios.forEach(m => {
          const opt = document.createElement('option');
          opt.value = m.nome;
          lista.appendChild(opt);
        });
        console.log(`✅ Municípios IBGE carregados: ${municipios.length}`);
      })
      .catch(err => console.error('Erro ao carregar municípios do IBGE:', err));

    // 🔹 Provedores básicos
    const provedoresPadrao = ['Starlink', 'Vivo', 'Claro', 'Oi', 'HughesNet', 'GVT', 'TIM', 'BR Digital', 'Sky', 'Prodepa'];
    const listaProvedores = document.getElementById('listaProvedores');
    if (listaProvedores) {
      listaProvedores.innerHTML = '';
      provedoresPadrao.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p;
        listaProvedores.appendChild(opt);
      });
      console.log('✅ Lista básica de provedores carregada.');
    }
  });
}
