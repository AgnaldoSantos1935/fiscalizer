// ====================================================
// 🌎 ibge.js — estados e municípios
// ====================================================
import { showToast } from '../utils/notifications';

export function bootIbgeHelpers() {
  // helpers vazios por enquanto (mantidos para compatibilidade futura)
}

// Carrega estados em um <select>
export async function carregarEstados(selectId) {
  const select = document.getElementById(selectId);
  if (!select) return;

  try {
    const res = await fetch('/api/ibge/estados');
    const estados = await res.json();
    estados.sort((a, b) => a.nome.localeCompare(b.nome));
    select.innerHTML = '<option value="">Selecione o Estado</option>';
    estados.forEach((uf) => {
      const opt = document.createElement('option');
      opt.value = uf.sigla;
      opt.textContent = `${uf.nome} (${uf.sigla})`;
      select.appendChild(opt);
    });
  } catch (err) {
    showToast('error', 'Erro ao carregar estados.');
    console.error('[IBGE estados]', err);
  }
}

// Carrega municípios por UF em um <select>
export async function carregarMunicipios(selectId, uf) {
  const select = document.getElementById(selectId);
  if (!select || !uf) return;

  try {
    const res = await fetch(`/api/ibge/municipios/${uf}`);
    const municipios = await res.json();
    municipios.sort((a, b) => a.nome.localeCompare(b.nome));
    select.innerHTML = '<option value="">Selecione o Município</option>';
    municipios.forEach((m) => {
      const opt = document.createElement('option');
      opt.value = m.nome;
      opt.textContent = m.nome;
      select.appendChild(opt);
    });
  } catch (err) {
    showToast('error', 'Erro ao carregar municípios.');
    console.error('[IBGE municipios]', err);
  }
}
