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

      if (campos.razao_social) {
        const elRazao = document.getElementById(campos.razao_social);
        if (elRazao) elRazao.value = data.razao_social || '';
      }
      if (campos.nome_fantasia) {
        const elFantasia = document.getElementById(campos.nome_fantasia);
        if (elFantasia) elFantasia.value = data.nome_fantasia || '';
      }
      if (campos.logradouro) {
        const elLogradouro = document.getElementById(campos.logradouro);
        if (elLogradouro) elLogradouro.value = data.logradouro || '';
      }
      if (campos.numero) {
        const elNumero = document.getElementById(campos.numero);
        if (elNumero) elNumero.value = data.numero || '';
      }
      if (campos.bairro) {
        const elBairro = document.getElementById(campos.bairro);
        if (elBairro) elBairro.value = data.bairro || '';
      }
      if (campos.municipio) {
        const elMunicipio = document.getElementById(campos.municipio);
        if (elMunicipio) elMunicipio.value = data.municipio || '';
      }
      if (campos.uf) {
        const elUf = document.getElementById(campos.uf);
        if (elUf) elUf.value = data.uf || '';
      }
      if (campos.cep) {
        const elCep = document.getElementById(campos.cep);
        if (elCep) elCep.value = (data.cep || '').replace(/(\d{5})(\d{3})/, '$1-$2');
      }

      showToast('info', 'Dados da empresa preenchidos automaticamente!');
    } catch (err) {
      showToast('error', 'Erro ao consultar CNPJ.');
      console.error('[BrasilAPI CNPJ]', err);
    }
  }, true);
}
