
// ==========================
// 🚀 APP.JS — Entrada principal Fiscalizer
// ==========================

// Core do Laravel (bootstrap, CSRF, Axios se quiser)
import './bootstrap';

// 🔹 Dependências globais
// jQuery global
import $ from 'jquery';
window.$ = window.jQuery = $;

// Bootstrap 5
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';

// Utilidades
import { showToast, confirmarAcao, setupToastrDefaults } from './utils/notifications';
import { applyMasksDelegation } from './utils/masks';
import { formatarMoeda } from './utils/helpers';

// Integrações
import { wireCepAutoFill } from './integrations/viacep';
import { wireCnpjAutoFill } from './integrations/brasilapi';
import { bootIbgeHelpers } from './integrations/ibge';

// Módulos de UI (somente se a página tiver os elementos)
import { inicializarModaisHosts } from './modules/modaisHosts';
import { bootFiscalizerAutocomplete } from './modules/fiscalizerAutocomplete';

// =======================
// ✨ Inicialização global
// =======================
document.addEventListener('DOMContentLoaded', () => {
  setupToastrDefaults();
  applyMasksDelegation();
  wireCepAutoFill();
  wireCnpjAutoFill();
  bootIbgeHelpers();
  bootFiscalizerAutocomplete();

  // Inicializações condicionais (evita erros em páginas sem os modais)
  if (document.querySelector('[data-modal-hosts]')) {
    inicializarModaisHosts();
  }
  // Removido: inicialização de modais de contratos (lógica agora inline na view)

  // Namespace global (opcional/legado)
  window.Fiscalizer = window.Fiscalizer || {};
  window.Fiscalizer.utils = { showToast, confirmarAcao, formatarMoeda };

  console.info('%c[Fiscalizer] main.js carregado com sucesso', 'color: #22c55e; font-weight: 700;');

  // --- Push (fallback seguro) ---
  window.subscribePush = async function subscribePush() {
    try {
      if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        showToast('error', 'Seu navegador não suporta notificações push.');
        return;
      }

      const perm = await Notification.requestPermission();
      if (perm !== 'granted') {
        showToast('error', 'Permissão de notificação negada.');
        return;
      }

      const swReg = await navigator.serviceWorker.register('/sw.js');
      const sub = await swReg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: undefined // Configure VAPID se disponível
      });

      const res = await fetch('/push/subscribe', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': window.CSRFToken
        },
        body: JSON.stringify(sub)
      });

      if (!res.ok) throw new Error('Falha ao registrar assinatura');
      const data = await res.json();
      if (data && data.success) {
        showToast('success', 'Notificações push ativadas com sucesso!');
      } else {
        showToast('error', 'Não foi possível ativar push agora.');
      }
    } catch (e) {
      console.error('[Push] erro:', e);
      showToast('error', 'Push não configurado. Contate o administrador.');
    }
  };

  // --- Echo: ouvir novas notificações em tempo real ---
  try {
    if (window.Echo && window.AppUserId) {
      window.Echo.private(`users.${window.AppUserId}`)
        .listen('NewNotification', (payload) => {
          const root = document.querySelector('.notif-dropdown');
          const badge = root?.querySelector('.navbar-badge');
          const menu = root?.querySelector('.dropdown-menu');
          const header = menu?.querySelector('.dropdown-header');
          const divider = menu?.querySelector('.dropdown-divider');

          const current = parseInt(badge?.textContent || '0', 10);
          const newCount = current + 1;
          if (badge) badge.textContent = newCount;
          if (root) root.classList.add('notif-pulse');
          if (header) header.textContent = `${newCount} Notificações`;

          if (menu && divider) {
            const a = document.createElement('a');
            a.href = payload.link || '#';
            a.className = 'dropdown-item';
            a.innerHTML = `<i class="fas fa-info-circle mr-2"></i> ${payload.titulo} <span class="float-right text-muted text-sm">agora</span>`;
            menu.insertBefore(a, divider);
          }

          if (window.Fiscalizer?.utils?.showToast) {
            window.Fiscalizer.utils.showToast('success', payload.titulo || 'Nova notificação');
          } else {
            console.info('[Notif] Nova:', payload);
          }
        });
    }
  } catch (e) {
    console.warn('[Echo] subscribe falhou:', e);
  }
});
