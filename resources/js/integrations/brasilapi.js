// ====================================================
// 🏢 brasilapi.js — consulta de CNPJ via rota da aplicação
// ====================================================
import { showToast } from '../utils/notifications';
import { cacheGet, cacheSet } from '../utils/helpers';

export function wireCnpjAutoFill() {
  document.addEventListener('blur', async (e) => {
    const el = e.target;
    if (el.id !== 'cnpj') return;

    const raw = (el.value || '').replace(/\D/g, '');
    if (raw.length !== 14) {
      showToast('warning', 'CNPJ inválido.');
      return;
    }

    const campos = {
      razao_social: el.dataset.razao_social,
      nome_fantasia: el.dataset.nome_fantasia,
      logradouro: el.dataset.logradouro,
      numero: el.dataset.numero,
      bairro: el.dataset.bairro,
      municipio: el.dataset.municipio,
      uf: el.dataset.uf,
      cep: el.dataset.cep,
    };

    const cacheKey = `cnpj:${raw}`;
    try {
      const cached = cacheGet(cacheKey);
      let data;
      if (cached) {
        data = cached;
      } else {
        const res = await fetch(`/api/brasilapi/cnpj/${raw}`);
        data = await res.json();
        cacheSet(cacheKey, data);
      }

      if (data.erro || data.message) {
        showToast('error', 'Não foi possível consultar o CNPJ.');
        return;
      }

      if (campos.razao_social) document.getElementById(campos.razao_social)?.value = data.razao_social || '';
      if (campos.nome_fantasia) document.getElementById(campos.nome_fantasia)?.value = data.nome_fantasia || '';
      if (campos.logradouro) document.getElementById(campos.logradouro)?.value = data.logradouro || '';
      if (campos.numero) document.getElementById(campos.numero)?.value = data.numero || '';
      if (campos.bairro) document.getElementById(campos.bairro)?.value = data.bairro || '';
      if (campos.municipio) document.getElementById(campos.municipio)?.value = data.municipio || '';
      if (campos.uf) document.getElementById(campos.uf)?.value = data.uf || '';
      if (campos.cep) document.getElementById(campos.cep)?.value = (data.cep || '').replace(/(\d{5})(\d{3})/, '$1-$2');

      showToast('info', 'Dados da empresa preenchidos automaticamente!');
    } catch (err) {
      showToast('error', 'Erro ao consultar CNPJ.');
      console.error('[BrasilAPI CNPJ]', err);
    }
  }, true);
}
