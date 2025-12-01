import axios from "axios";
import $ from "jquery";
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

  const reloadPage = () => window.location.reload();

  // 🔹 Configuração base
  const cfgPadrao = {
    responsive: true,
    language: { url: "/datatables/pt-BR.json" },
    columnDefs: [{ targets: "_all", className: "align-middle" }],
  };

  // ===============================================================
  // 🔸 1. REQUISITOS
  // ===============================================================
  // Listas renderizadas server-side; sem DataTables

  // ===============================================================
  // 🔸 2. ATIVIDADES
  // ===============================================================
  

  // ===============================================================
  // 🔸 3. CRONOGRAMA
  // ===============================================================
  

  // ===============================================================
  // 🔸 4. EQUIPE
  // ===============================================================
  

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

        reloadPage();
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
      reloadPage();
    } catch (err) {
      toastr.error("Erro ao excluir registro.");
    }
  });
});
