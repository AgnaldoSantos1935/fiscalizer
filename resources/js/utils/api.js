// ====================================================
// ⚙️ api.js — helpers de comunicação (fetch) com CSRF
// ====================================================
import { showToast } from './notifications';

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.content : '';
}

export async function getJSON(url) {
  try {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`Erro ${res.status}`);
    return await res.json();
  } catch (err) {
    showToast('error', `Falha ao carregar: ${err.message}`);
    console.error('[GET JSON]', err);
    return { error: true, message: err.message };
  }
}

export async function postJSON(url, data = {}) {
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      body: JSON.stringify(data),
    });
    if (!res.ok) throw new Error(`Erro ${res.status}`);
    return await res.json();
  } catch (err) {
    showToast('error', `Falha ao enviar: ${err.message}`);
    console.error('[POST JSON]', err);
    return { error: true, message: err.message };
  }
}
