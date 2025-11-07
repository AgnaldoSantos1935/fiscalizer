import './bootstrap';
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

// DataTables + Bootstrap 5 + Responsive
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-responsive-bs5';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';

// Confirma carregamento
console.log('✅ jQuery, Bootstrap e DataTables carregados via Vite');


// 🔹 Seu JS central (com todas as funções e integrações)
import * as Global from './global';
window.Fiscalizer = Global; // acesso global no Blade

// Log de controle
console.info("%cFiscalizer JS inicializado via Vite", "color: #1d9bf0; font-weight:bold;");


