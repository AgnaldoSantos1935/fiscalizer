// ====================================================
// 🛠 helpers.js — utilidades genéricas
// ====================================================

export function formatarMoeda(valor) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor ?? 0);
}

export function debounce(fn, delay = 300) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), delay);
  };
}

// Cache simples em memória para evitar requisições duplicadas
const cache = new Map();

export function cacheGet(key) { return cache.get(key); }
export function cacheSet(key, value) { cache.set(key, value); }
