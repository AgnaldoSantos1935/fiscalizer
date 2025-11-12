import axios from "axios";
import $ from "jquery";
import "datatables.net-bs5";
import "datatables.net-responsive-bs5";
import toastr from "toastr";

document.addEventListener("DOMContentLoaded", () => {
  const projetoId = window.projetoId;

  // 🔹 Função utilitária: abre modal e preenche campos
  const abrirModalEdicao = (formId, data) => {
    const form = document.querySelector(formId);
    Object.entries(data).forEach(([key, val]) => {
      const field = form.querySelector(`[name="${key}"]`);
      if (field) field.value = val ?? "";
    });
    form.dataset.id = data.id;
    const modal = new bootstrap.Modal(form.closest(".modal"));
    modal.show();
  };

  // 🔹 Função utilitária: limpar modal após salvar
  const limparModal = (formId) => {
    const form = document.querySelector(formId);
    form.reset();
    delete form.dataset.id;
  };

  // 🔹 Função padrão de atualização de tabela
  const atualizarTabela = (selector) => {
    if ($.fn.DataTable.isDataTable(selector)) {
      $(selector).DataTable().ajax.reload(null, false);
    }
  };

  // 🔹 Configuração base
  const cfgPadrao = {
    responsive: true,
    language: { url: "/datatables/pt-BR.json" },
    columnDefs: [{ targets: "_all", className: "align-middle" }],
  };

  // ===============================================================
  // 🔸 1. REQUISITOS
  // ===============================================================
  const tabelaRequisitos = $("#tabelaRequisitos").DataTable({
    ...cfgPadrao,
    ajax: `/api/projetos/${projetoId}/requisitos`,
    columns: [
      { data: "descricao" },
      { data: "tipo" },
      { data: "complexidade" },
      { data: "pontos_funcao", className: "text-end" },
      { data: "responsavel" },
      {
        data: null,
        className: "text-center",
        render: (d) => `
          <button class="btn btn-sm btn-primary btn-editar" data-id="${d.id}" data-tipo="requisito"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger btn-excluir" data-id="${d.id}" data-tipo="requisito"><i class="fas fa-trash"></i></button>
        `,
      },
    ],
  });

  // ===============================================================
  // 🔸 2. ATIVIDADES
  // ===============================================================
  const tabelaAtividades = $("#tabelaAtividades").DataTable({
    ...cfgPadrao,
    ajax: `/api/projetos/${projetoId}/atividades`,
    columns: [
      { data: "etapa" },
      { data: "analista" },
      { data: "data", className: "text-center" },
      { data: "horas", className: "text-end" },
      {
        data: null,
        className: "text-center",
        render: (d) => `
          <button class="btn btn-sm btn-primary btn-editar" data-id="${d.id}" data-tipo="atividade"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger btn-excluir" data-id="${d.id}" data-tipo="atividade"><i class="fas fa-trash"></i></button>
        `,
      },
    ],
  });

  // ===============================================================
  // 🔸 3. CRONOGRAMA
  // ===============================================================
  const tabelaCronograma = $("#tabelaCronograma").DataTable({
    ...cfgPadrao,
    ajax: `/api/projetos/${projetoId}/cronograma`,
    columns: [
      { data: "etapa" },
      { data: "responsavel" },
      { data: "data_inicio", className: "text-center" },
      { data: "data_fim", className: "text-center" },
      { data: "status", className: "text-center" },
      {
        data: null,
        className: "text-center",
        render: (d) => `
          <button class="btn btn-sm btn-primary btn-editar" data-id="${d.id}" data-tipo="cronograma"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger btn-excluir" data-id="${d.id}" data-tipo="cronograma"><i class="fas fa-trash"></i></button>
        `,
      },
    ],
  });

  // ===============================================================
  // 🔸 4. EQUIPE
  // ===============================================================
  const tabelaEquipe = $("#tabelaEquipe").DataTable({
    ...cfgPadrao,
    ajax: `/api/projetos/${projetoId}/equipe`,
    columns: [
      { data: "pessoa.nome_completo" },
      { data: "papel" },
      { data: "horas_previstas", className: "text-end" },
      { data: "horas_realizadas", className: "text-end" },
      {
        data: null,
        className: "text-center",
        render: (d) => `
          <button class="btn btn-sm btn-primary btn-editar" data-id="${d.id}" data-tipo="equipe"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger btn-excluir" data-id="${d.id}" data-tipo="equipe"><i class="fas fa-trash"></i></button>
        `,
      },
    ],
  });

  // ===============================================================
  // 🔸 CRUD Genérico (Adicionar / Editar)
  // ===============================================================
  const forms = {
    requisito: "#formRequisito",
    atividade: "#formAtividade",
    cronograma: "#formCronograma",
    equipe: "#formEquipe",
  };

  Object.entries(forms).forEach(([tipo, formId]) => {
    const form = document.querySelector(formId);
    if (!form) return;

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const data = new FormData(form);
      const id = form.dataset.id;
      const url = id
        ? `/${tipo === "equipe" ? "equipe" : tipo + "s"}/${id}`
        : form.getAttribute("action");
      const method = id ? "put" : "post";

      try {
        await axios({ method, url, data });
        toastr.success(id ? "Registro atualizado!" : "Registro adicionado!");
        const modal = bootstrap.Modal.getInstance(form.closest(".modal"));
        modal.hide();
        limparModal(formId);

        if (tipo === "requisito") atualizarTabela("#tabelaRequisitos");
        if (tipo === "atividade") atualizarTabela("#tabelaAtividades");
        if (tipo === "cronograma") atualizarTabela("#tabelaCronograma");
        if (tipo === "equipe") atualizarTabela("#tabelaEquipe");
      } catch (err) {
        console.error(err);
        toastr.error("Erro ao salvar registro.");
      }
    });
  });

  // ===============================================================
  // 🔸 Editar (carregar dados no modal)
  // ===============================================================
  $(document).on("click", ".btn-editar", async function () {
    const id = $(this).data("id");
    const tipo = $(this).data("tipo");
    const url = `/${tipo === "equipe" ? "equipe" : tipo + "s"}/${id}`;

    try {
      const res = await axios.get(url);
      abrirModalEdicao(forms[tipo], res.data);
    } catch (err) {
      toastr.error("Erro ao carregar dados para edição.");
    }
  });

  // ===============================================================
  // 🔸 Excluir
  // ===============================================================
  $(document).on("click", ".btn-excluir", async function () {
    const id = $(this).data("id");
    const tipo = $(this).data("tipo");
    const url = `/${tipo === "equipe" ? "equipe" : tipo + "s"}/${id}`;

    if (!confirm("Deseja realmente excluir este registro?")) return;
    try {
      await axios.delete(url);
      toastr.success("Registro excluído com sucesso!");
      if (tipo === "requisito") atualizarTabela("#tabelaRequisitos");
      if (tipo === "atividade") atualizarTabela("#tabelaAtividades");
      if (tipo === "cronograma") atualizarTabela("#tabelaCronograma");
      if (tipo === "equipe") atualizarTabela("#tabelaEquipe");
    } catch (err) {
      toastr.error("Erro ao excluir registro.");
    }
  });
});
