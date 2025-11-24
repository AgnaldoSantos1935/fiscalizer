
// ==========================
// 🚀 APP.JS — Entrada principal Fiscalizer
// ==========================

// Core do Laravel (bootstrap, CSRF, Axios se quiser)
import './bootstrap';

// 🔹 Dependências globais


// Bootstrap 5
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';

// Tema fiscalizaer
import '../css/fiscalizer-theme.css';
import '../css/fiscalizer-theme-extras.css';

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

  // Tabs TR (create/edit)
  const trNav = document.querySelector('.tr-nav');
  if (trNav) {
    const tabs = Array.from(document.querySelectorAll('.tr-tab'));
    const showTab = (name) => {
      tabs.forEach(t => t.classList.add('d-none'));
      const target = document.querySelector(`.tr-tab[data-tab="${name}"]`);
      if (target) {
        target.classList.remove('d-none');
        target.style.animation = 'fadeIn .18s ease';
      }
      trNav.querySelectorAll('.nav-link').forEach(a => a.classList.remove('active'));
      const link = trNav.querySelector(`.nav-link[data-tab="${name}"]`);
      if (link) link.classList.add('active');
    };
    const initial = trNav.querySelector('.nav-link.active')?.dataset.tab || 'geral';
    showTab(initial);
    trNav.addEventListener('click', (e) => {
      const a = e.target.closest('.nav-link');
      if (!a) return;
      e.preventDefault();
      const name = a.dataset.tab;
      if (name) showTab(name);
    });
  }

  const trEditors = Array.from(document.querySelectorAll('.tr-editor'));
  const initSummernoteEditors = () => {
    const JQ = window.jQuery || window.$;
    if (!(JQ && JQ.fn && JQ.fn.summernote)) return;
    trEditors.forEach(editor => {
      if (editor.nextElementSibling && editor.nextElementSibling.classList?.contains('note-editor')) return;
      (window.jQuery || window.$)(editor).summernote({
        minHeight: 120,
        maxHeight: 600,
        placeholder: 'Digite seu conteúdo',
        toolbar: [["style", ["bold", "italic", "underline", "clear"]], ["para", ["ul", "ol", "paragraph"]], ["insert", ["link"]], ["view", ["fullscreen", "codeview"]]],
        callbacks: {
          onInit: function () {
            const $editable = (window.jQuery || window.$)(this).next('.note-editor').find('.note-editable');
            const minH = 120, maxH = 600;
            $editable.css({'overflow-y': 'hidden'});
            const resize = function() {
              if (!$editable[0]) return;
              $editable[0].style.height = 'auto';
              const h = Math.max(minH, Math.min($editable[0].scrollHeight, maxH));
              $editable[0].style.height = h + 'px';
            };
            resize();
            $editable.on('input', resize);
          },
          onKeyup: function () {
            const $editable = (window.jQuery || window.$)(this).next('.note-editor').find('.note-editable');
            $editable.trigger('input');
          },
          onPaste: function () {
            const $editable = (window.jQuery || window.$)(this).next('.note-editor').find('.note-editable');
            setTimeout(function(){ $editable.trigger('input'); }, 0);
          }
        }
      });
    });
  };

  if (trEditors.length) {
    const JQ2 = window.jQuery || window.$;
    if (JQ2 && JQ2.fn && JQ2.fn.summernote) {
      initSummernoteEditors();
    } else {
      const hasCss = Array.from(document.styleSheets).some(s => String(s.href || '').includes('summernote'));
      const hasJs = Array.from(document.scripts).some(s => String(s.src || '').includes('summernote'));
      if (!hasCss) {
        const css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = '/vendor/adminlte/plugins/summernote/summernote-bs5.min.css';
        document.head.appendChild(css);
      }
      if (!hasJs) {
        const js = document.createElement('script');
        js.src = '/vendor/adminlte/plugins/summernote/summernote-bs5.min.js';
        js.onload = () => {
          initSummernoteEditors();
          if (!((window.jQuery || window.$)?.fn?.summernote)) {
            const jsCdn = document.createElement('script');
            jsCdn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js';
            jsCdn.onload = initSummernoteEditors;
            document.head.appendChild(jsCdn);
          }
        };
        document.head.appendChild(js);
      } else {
        setTimeout(initSummernoteEditors, 0);
      }
      setTimeout(() => {
        const JQ3 = window.jQuery || window.$;
        if (!(JQ3 && JQ3.fn && JQ3.fn.summernote)) {
          const jsCdn2 = document.createElement('script');
          jsCdn2.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js';
          jsCdn2.onload = initSummernoteEditors;
          document.head.appendChild(jsCdn2);
          if (!Array.from(document.styleSheets).some(s => String(s.href || '').includes('summernote-lite.min.css'))) {
            const cssCdn2 = document.createElement('link');
            cssCdn2.rel = 'stylesheet';
            cssCdn2.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css';
            document.head.appendChild(cssCdn2);
          }
        }
      }, 1500);
    }
  }

  const applyTheme = (dark) => {
    document.body.classList.toggle('theme-dark', !!dark);
    const icon = document.getElementById('toggleThemeIcon');
    if (icon) {
      icon.classList.toggle('fa-moon', !dark);
      icon.classList.toggle('fa-sun', !!dark);
    }
    try {
      const C = window.Chart;
      if (C && C.defaults) {
        const text = dark ? '#d0d7de' : '#1b1b18';
        const grid = dark ? '#3E3E3A' : '#e4e8f0';
        C.defaults.color = text;
        if (C.defaults.plugins && C.defaults.plugins.legend && C.defaults.plugins.legend.labels) {
          C.defaults.plugins.legend.labels.color = text;
        }
        C.defaults.scale = C.defaults.scale || {};
        C.defaults.scale.grid = Object.assign({}, C.defaults.scale.grid || {}, { color: grid });
        C.defaults.scale.ticks = Object.assign({}, C.defaults.scale.ticks || {}, { color: text });
      }
    } catch (e) {}
    window.dispatchEvent(new CustomEvent('theme:change', { detail: { dark: !!dark } }));
  };
  const savedTheme = localStorage.getItem('theme-dark') === 'true';
  applyTheme(savedTheme);
  window.Theme = {
    onChange: function (fn) {
      window.addEventListener('theme:change', function (e) { try { fn(!!(e.detail && e.detail.dark)); } catch(_) {} });
    },
    isDark: function () { return document.body.classList.contains('theme-dark'); }
  };
  const btn = document.getElementById('toggleTheme');
  if (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const next = !document.body.classList.contains('theme-dark');
      applyTheme(next);
      localStorage.setItem('theme-dark', next ? 'true' : 'false');
    });
  }

  try {
    if (window.$ && $.fn && $.fn.dataTable) {
      $.extend(true, $.fn.dataTable.defaults, {
        paging: true,
        pageLength: 10,
        lengthChange: true,
        ordering: true,
        searching: false,
        language: { url: '/js/pt-BR.json' },
        dom: 't<"bottom"p>',
        responsive: true
      });
      const autoTables = Array.from(document.querySelectorAll('table.table'));
      autoTables.forEach(function(tbl){
        const $t = $(tbl);
        if ($t.hasClass('dt-skip')) return;
        if ($t.hasClass('dataTable')) return;
        if (!tbl.querySelector('thead')) return;
        try { $t.DataTable(); } catch(_) {}
      });
    }
  } catch (_) {}

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
document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("sidebarToggle");
    toggle?.addEventListener("click", () => {
        document.body.classList.toggle("sidebar-condensed");
    });
});
document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("mobileSidebarToggle");
    const overlay = document.querySelector(".sidebar-overlay");

    if (toggle) {
        toggle.addEventListener("click", () => {
            document.body.classList.toggle("sidebar-open");
        });
    }

    if (overlay) {
        overlay.addEventListener("click", () => {
            document.body.classList.remove("sidebar-open");
        });
    }
});
