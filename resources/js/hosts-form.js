document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");
    if (!form) return;

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const action = form.getAttribute("action");
        const method = form.querySelector("input[name='_method']")?.value || "POST";

        // Validações simples
        const nome = formData.get("nome_conexao");
        const alvo = formData.get("host_alvo");

        if (!nome || !alvo) {
            showToast("error", "Preencha todos os campos obrigatórios.");
            return;
        }

        try {
            const response = await fetch(action, {
                method: method === "PUT" ? "POST" : method,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("input[name='_token']").value
                },
                body: formData
            });

            if (!response.ok) {
                let text = await response.text();
                console.error("Resposta do servidor:", text);
                showToast("error", "Erro ao salvar. Verifique os campos.");
                return;
            }

            const data = await response.json();

            if (data.success) {
                showToast("success", data.message || "Salvo com sucesso!");

                // Redirecionar após salvar
                setTimeout(() => {
                    window.location.href = data.redirect || "/hosts";
                }, 1000);
            }

        } catch (error) {
            console.error(error);
            showToast("error", "Falha ao conectar ao servidor.");
        }
    });
});

/* -------------------
   FUNÇÃO DE TOAST
-------------------- */
function showToast(type, message) {
    let toastEl, messageEl;

    if (type === "success") {
        toastEl = document.getElementById("toastSuccess");
        messageEl = document.getElementById("toastSuccessMsg");
    } else {
        toastEl = document.getElementById("toastError");
        messageEl = document.getElementById("toastErrorMsg");
    }

    messageEl.textContent = message;

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}
