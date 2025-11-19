// ====================================================
// 🧾 masks.js — máscaras e delegação
// ====================================================

export function mascaraTelefone(tel) {
  tel = tel.replace(/\D/g, '');
  if (tel.length <= 10) tel = tel.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
  else tel = tel.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
  return tel;
}

export function mascaraCNPJ(cnpj) {
  cnpj = cnpj.replace(/\D/g, '');
  cnpj = cnpj.replace(/^(\d{2})(\d)/, '$1.$2');
  cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
  cnpj = cnpj.replace(/\.(\d{3})(\d)/, '.$1/$2');
  cnpj = cnpj.replace(/(\d{4})(\d)/, '$1-$2');
  return cnpj;
}

export function mascaraCEP(cep) {
  cep = cep.replace(/\D/g, '');
  cep = cep.replace(/^(\d{5})(\d)/, '$1-$2');
  return cep.substring(0, 9);
}

export function mascaraCPF(cpf) {
  cpf = cpf.replace(/\D/g, '');
  cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
  cpf = cpf.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
  cpf = cpf.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{2})/, '$1.$2.$3-$4');
  return cpf.substring(0, 14);
}

function parseNumberBR(value) {
  if (typeof value !== 'string') value = String(value ?? '');
  const sanitized = value.replace(/[^\d,\.]/g, '').replace(/\./g, '').replace(/,(?=\d{0,2}$)/, '.$1');
  // última vírgula vira ponto para parseFloat
  const normalized = sanitized.replace(/,(?=\d{2}$)/, '.');
  const num = parseFloat(normalized);
  return isNaN(num) ? 0 : num;
}

function formatMoneyBR(n, decimals = 2) {
  try {
    return (n ?? 0).toLocaleString('pt-BR', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
  } catch (_) {
    return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(n ?? 0);
  }
}

function mascaraMoneyBR(el) {
  const decimals = parseInt(el.dataset?.decimals || '2', 10);
  const n = parseNumberBR(el.value);
  el.value = formatMoneyBR(n, decimals);
}

export function applyMasksDelegation() {
  document.addEventListener('input', (e) => {
    const el = e.target;
    if (el.classList?.contains('mask-telefone')) el.value = mascaraTelefone(el.value);
    if (el.classList?.contains('mask-cnpj') || el.classList?.contains('cnpj-input')) el.value = mascaraCNPJ(el.value);
    if (el.classList?.contains('mask-cpf') || el.classList?.contains('cpf-input')) el.value = mascaraCPF(el.value);
    if (el.classList?.contains('mask-cep') || el.classList?.contains('cep-input')) el.value = mascaraCEP(el.value);
  });

  document.addEventListener('blur', (e) => {
    const el = e.target;
    if (el.classList?.contains('money-br-input')) mascaraMoneyBR(el);
  }, true);
}
