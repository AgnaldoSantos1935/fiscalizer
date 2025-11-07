import './bootstrap';
// ==========================
// 🚀 APP.JS — Entrada principal Fiscalizer
// ==========================

// Core do Laravel (bootstrap, CSRF, Axios se quiser)
import './bootstrap';

// 🔹 Dependências globais
import 'jquery';
import 'bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle';
import 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'sweetalert2/dist/sweetalert2.all';
import 'toastr';
import '@fortawesome/fontawesome-free/js/all';
import axios from 'axios';
import Chart from 'chart.js/auto';

// 🔹 Seu JS central (com todas as funções e integrações)
import * as Global from './global';
window.Fiscalizer = Global; // acesso global no Blade

// Log de controle
console.info("%cFiscalizer JS inicializado via Vite", "color: #1d9bf0; font-weight:bold;");


