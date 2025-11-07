// ====================================================
// 🔧 GLOBAL.JS — Núcleo JavaScript do sistema Fiscalizer
// ====================================================

// Dependências externas
import toastr from "toastr";
import Swal from "sweetalert2";
import "toastr/build/toastr.min.css";
// Importa e inicializa o módulo de Hosts
import { inicializarModaisHosts } from './modules/modaisHosts';
inicializarModaisHosts();

// Importa e inicializa o arquivo modaisContratos
import { inicializarModaisContratos } from './modules/modaisContratos';
inicializarModaisContratos();

// Também adiciona ao namespace global
window.Contratos = window.Contratos || {};
window.Contratos.abrirModalItem = window.abrirModalItem;
window.Contratos.abrirModalEmpenho = window.abrirModalEmpenho;
window.Contratos.abrirModalPagamento = window.abrirModalPagamento;
// =======================
// 🔔 Funções de feedback visual
// =======================
export function showToast(type, message, title = '') {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-bottom-right',
        timeOut: 4000,
    };
    toastr[type](message, title);
}

export function confirmarAcao(msg, callback) {
    Swal.fire({
        title: "Confirmação",
        text: msg,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim",
        cancelButtonText: "Cancelar",
    }).then(result => {
        if (result.isConfirmed) callback();
    });
}

// =======================
// ⚙️ Funções utilitárias e AJAX
// =======================
export async function postData(url, data = {}) {
    const response = await fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(data),
    });
    return response.json();
}

export function formatarMoeda(valor) {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(valor);
}

// =======================
// 🧾 Máscaras e Validações
// =======================
export function mascaraTelefone(tel) {
    tel = tel.replace(/\D/g, "");
    if (tel.length <= 10) tel = tel.replace(/(\d{2})(\d{4})(\d{0,4})/, "($1) $2-$3");
    else tel = tel.replace(/(\d{2})(\d{5})(\d{0,4})/, "($1) $2-$3");
    return tel;
}

export function mascaraCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, "");
    cnpj = cnpj.replace(/^(\d{2})(\d)/, "$1.$2");
    cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
    cnpj = cnpj.replace(/\.(\d{3})(\d)/, ".$1/$2");
    cnpj = cnpj.replace(/(\d{4})(\d)/, "$1-$2");
    return cnpj;
}

export function mascaraCEP(cep) {
    cep = cep.replace(/\D/g, "");
    cep = cep.replace(/^(\d{5})(\d)/, "$1-$2");
    return cep.substring(0, 9);
}

export function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

export function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]+/g, '');
    if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;

    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0)) return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }

    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    return resultado == digitos.charAt(1);
}

// =======================
// 📍 ViaCEP — busca automática de endereço
// =======================
export async function buscarEnderecoPorCEP(cep, campos = {}) {
    cep = cep.replace(/\D/g, "");
    if (cep.length !== 8) return;

    try {
        const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await res.json();

        if (data.erro) {
            showToast('warning', 'CEP não encontrado.');
            return;
        }

        if (campos.logradouro) document.getElementById(campos.logradouro).value = data.logradouro || '';
        if (campos.bairro) document.getElementById(campos.bairro).value = data.bairro || '';
        if (campos.cidade) document.getElementById(campos.cidade).value = data.localidade || '';
        if (campos.uf) document.getElementById(campos.uf).value = data.uf || '';

        showToast('info', 'Endereço preenchido automaticamente.');
    } catch {
        showToast('error', 'Erro ao buscar CEP.');
    }
}

// =======================
// 🏢 BrasilAPI — consulta de CNPJ
// =======================
export async function consultarCnpj(cnpj, campos = {}) {
    cnpj = cnpj.replace(/\D/g, "");
    if (cnpj.length !== 14) {
        showToast('warning', 'CNPJ inválido.');
        return;
    }

    try {
        const response = await fetch(`/api/brasilapi/cnpj/${cnpj}`);
        const data = await response.json();

        if (data.erro || data.message) {
            showToast('error', 'Não foi possível consultar o CNPJ.');
            return;
        }

        if (campos.razao_social) document.getElementById(campos.razao_social).value = data.razao_social || '';
        if (campos.nome_fantasia) document.getElementById(campos.nome_fantasia).value = data.nome_fantasia || '';
        if (campos.logradouro) document.getElementById(campos.logradouro).value = data.logradouro || '';
        if (campos.numero) document.getElementById(campos.numero).value = data.numero || '';
        if (campos.bairro) document.getElementById(campos.bairro).value = data.bairro || '';
        if (campos.municipio) document.getElementById(campos.municipio).value = data.municipio || '';
        if (campos.uf) document.getElementById(campos.uf).value = data.uf || '';
        if (campos.cep) document.getElementById(campos.cep).value = mascaraCEP(data.cep || '');

        showToast('info', 'Dados da empresa preenchidos automaticamente!');
    } catch {
        showToast('error', 'Erro ao consultar CNPJ.');
    }
}

// =======================
// 🌎 IBGE — Estados e Municípios
// =======================
export async function carregarEstados(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;

    try {
        const res = await fetch('/api/ibge/estados');
        const estados = await res.json();

        estados.sort((a, b) => a.nome.localeCompare(b.nome));
        select.innerHTML = '<option value="">Selecione o Estado</option>';

        estados.forEach(uf => {
            const opt = document.createElement('option');
            opt.value = uf.sigla;
            opt.textContent = `${uf.nome} (${uf.sigla})`;
            select.appendChild(opt);
        });
    } catch {
        showToast('error', 'Erro ao carregar estados.');
    }
}

export async function carregarMunicipios(selectId, uf) {
    const select = document.getElementById(selectId);
    if (!select || !uf) return;

    try {
        const res = await fetch(`/api/ibge/municipios/${uf}`);
        const municipios = await res.json();

        municipios.sort((a, b) => a.nome.localeCompare(b.nome));
        select.innerHTML = '<option value="">Selecione o Município</option>';

        municipios.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.nome;
            opt.textContent = m.nome;
            select.appendChild(opt);
        });
    } catch {
        showToast('error', 'Erro ao carregar municípios.');
    }
}

// =======================
// 💬 Aplicações automáticas de máscaras e eventos
// =======================
document.addEventListener("input", e => {
    const el = e.target;
    if (el.classList.contains("mask-telefone")) el.value = mascaraTelefone(el.value);
    if (el.classList.contains("mask-cnpj")) el.value = mascaraCNPJ(el.value);
    if (el.classList.contains("mask-cep")) el.value = mascaraCEP(el.value);
});

document.addEventListener("blur", e => {
    const el = e.target;

    // Busca endereço por CEP
    if (el.classList.contains("mask-cep")) {
        const campos = {
            logradouro: el.dataset.logradouro,
            bairro: el.dataset.bairro,
            cidade: el.dataset.cidade,
            uf: el.dataset.uf
        };
        buscarEnderecoPorCEP(el.value, campos);
    }

    // Consulta empresa por CNPJ
    if (el.id === "cnpj") {
        const campos = {
            razao_social: el.dataset.razao_social,
            nome_fantasia: el.dataset.nome_fantasia,
            logradouro: el.dataset.logradouro,
            numero: el.dataset.numero,
            bairro: el.dataset.bairro,
            municipio: el.dataset.municipio,
            uf: el.dataset.uf,
            cep: el.dataset.cep
        };
        consultarCnpj(el.value, campos);
    }
}, true);

// =======================
// ✨ Inicialização global
// =======================
document.addEventListener("DOMContentLoaded", () => {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    console.info("%c[Global.js] Fiscalizer carregado com sucesso", "color: green");
});

// ==========================================================
// 🌎 Fiscalizer – Autocomplete de Município (IBGE) e Provedor (BrasilAPI)
// ==========================================================
document.addEventListener("DOMContentLoaded", function () {

  // 🔹 IBGE Localidades API — municípios do Pará (UF 15)
  fetch('https://servicodados.ibge.gov.br/api/v1/localidades/estados/15/municipios')
    .then(resp => resp.json())
    .then(municipios => {
      const lista = document.getElementById('listaMunicipios');
      lista.innerHTML = '';
      municipios.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.nome;
        lista.appendChild(opt);
      });
      console.log(`✅ Municípios IBGE carregados: ${municipios.length}`);
    })
    .catch(err => console.error('Erro ao carregar municípios do IBGE:', err));

  // 🔹 BrasilAPI — provedores mais comuns
  const provedoresPadrao = ['Starlink', 'Vivo', 'Claro', 'Oi', 'HughesNet', 'GVT', 'TIM', 'BR Digital', 'Sky', 'Prodepa'];

  const listaProvedores = document.getElementById('listaProvedores');
  listaProvedores.innerHTML = '';
  provedoresPadrao.forEach(p => {
    const opt = document.createElement('option');
    opt.value = p;
    listaProvedores.appendChild(opt);
  });

  console.log('✅ Lista básica de provedores carregada.');
});
