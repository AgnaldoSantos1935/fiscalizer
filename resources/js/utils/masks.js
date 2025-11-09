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

export function applyMasksDelegation() {
  document.addEventListener('input', (e) => {
    const el = e.target;
    if (el.classList?.contains('mask-telefone')) el.value = mascaraTelefone(el.value);
    if (el.classList?.contains('mask-cnpj')) el.value = mascaraCNPJ(el.value);
    if (el.classList?.contains('mask-cep')) el.value = mascaraCEP(el.value);
  });
}
