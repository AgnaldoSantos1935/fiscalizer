// ====================================================
// 📍 viacep.js — busca automática de endereço por CEP
// ====================================================
import { showToast } from '../utils/notifications';
import { cacheGet, cacheSet } from '../utils/helpers';

export function wireCepAutoFill() {
  document.addEventListener('blur', async (e) => {
    const el = e.target;
    if (!el.classList?.contains('mask-cep')) return;

    let cep = (el.value || '').replace(/\D/g, '');
    if (cep.length !== 8) return;

    const campos = {
      logradouro: el.dataset.logradouro,
      bairro: el.dataset.bairro,
      cidade: el.dataset.cidade,
      uf: el.dataset.uf,
    };

    const cacheKey = `viacep:${cep}`;
    try {
      const cached = cacheGet(cacheKey);
      let data;
      if (cached) {
        data = cached;
      } else {
        const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        data = await res.json();
        cacheSet(cacheKey, data);
      }

      if (data.erro) {
        showToast('warning', 'CEP não encontrado.');
        return;
      }

      // ✅ Corrigido — sem optional chaining em atribuições
      if (campos.logradouro) {
        const input = document.getElementById(campos.logradouro);
        if (input) input.value = data.logradouro || '';
      }

      if (campos.bairro) {
        const input = document.getElementById(campos.bairro);
        if (input) input.value = data.bairro || '';
      }

      if (campos.cidade) {
        const input = document.getElementById(campos.cidade);
        if (input) input.value = data.localidade || '';
      }

      if (campos.uf) {
        const input = document.getElementById(campos.uf);
        if (input) input.value = data.uf || '';
      }

      showToast('info', 'Endereço preenchido automaticamente.');
    } catch (err) {
      showToast('error', 'Erro ao buscar CEP.');
      console.error('[ViaCEP]', err);
    }
  }, true);
}
