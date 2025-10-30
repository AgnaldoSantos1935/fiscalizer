@extends('layouts.app')

@section('title', 'Escolas')

@section('content')
<div class="container-fluid">

    <!-- 🔍 Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
            </h5>
        </div>

        <div class="card-body bg-white">
            <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm">
                <div class="col-md-3">
                    <label for="filtroCodigo" class="form-label fw-semibold small text-secondary">Código</label>
                    <input type="text" id="filtroCodigo" class="form-control form-control-sm" placeholder="Ex: 001, 045...">
                </div>

                <div class="col-md-4">
                    <label for="filtroNome" class="form-label fw-semibold small text-secondary">Nome da Escola</label>
                    <input type="text" id="filtroNome" class="form-control form-control-sm" placeholder="Digite parte do nome">
                </div>

                <div class="col-md-3">
                    <label for="filtroMunicipio" class="form-label fw-semibold small text-secondary">Município</label>
                    <input type="text" id="filtroMunicipio" class="form-control form-control-sm" placeholder="Belém, Santarém...">
                </div>

                <div class="col-md-2">
                    <label for="filtroUF" class="form-label fw-semibold small text-secondary">UF</label>
                    <input type="text" id="filtroUF" class="form-control form-control-sm" maxlength="2" placeholder="PA">
                </div>

                <div class="col-12 text-end mt-2">
                    <button type="button" id="btnAplicarFiltros" class="btn btn-primary btn-sm px-3 me-2">
                        <i class="fas fa-filter me-1"></i> Aplicar
                    </button>
                    <button type="button" id="btnLimparFiltros" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="fas fa-undo me-1"></i> Limpar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🏫 Lista de Escolas -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-school me-2 text-primary"></i>Escolas Cadastradas
            </h4>
        </div>

        <div class="card-body bg-white">

            <!-- 🔹 Navbar de ações -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 mb-4 shadow-sm">
                <div class="container-fluid">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item me-3">
                            <a id="navDetalhes" class="nav-link text-dark fw-semibold disabled" href="#">
                                <i class="fas fa-eye text-info me-1"></i> Detalhes
                            </a>
                        </li>
                        <li class="nav-item me-3">
                            <a id="navEditar" class="nav-link text-dark fw-semibold disabled" href="#">
                                <i class="fas fa-edit text-warning me-1"></i> Editar
                            </a>
                        </li>
                        <li class="nav-item me-3">
                            <a id="navExcluir" class="nav-link text-dark fw-semibold disabled" href="#">
                                <i class="fas fa-trash text-danger me-1"></i> Excluir
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a href="{{ route('escolas.create') }}" class="btn btn-primary btn-sm px-3">
                                <i class="fas fa-plus-circle me-1"></i> Nova Escola
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- 🔹 Tabela -->

                <table id="tabelaEscolas" class="table table-striped no-inner-borders w-100">
                <thead class="bg-light text-secondary border-bottom">
                    <tr>
                        <th class="text-center" style="width: 45px;">#</th>
                        <th>Inep</th>
                        <th>Nome da Escola</th>
                        <th>Município</th>
                        <th>UF</th>
                        <th>DRE</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>
</div>

<!-- 🔹 Modal Detalhes -->
<div class="modal fade" id="modalDetalhesEscola" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detalhes da Escola</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <table class="table table-borderless">
                    <tbody id="detalhesEscola"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
.table-borderless tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease-in-out;
}
.nav-link.disabled {
    opacity: 0.5;
    pointer-events: none;
}
#formFiltros input {
  border-radius: 10px;
}
#formFiltros .btn {
  border-radius: 20px;
}
/* Bordas externas apenas */
table.dataTable {
  border: 1px solid #dee2e6 !important;
  border-collapse: collapse !important;
  width: 100% !important;
}

table.dataTable th,
table.dataTable td {
  border: none !important;
  vertical-align: middle !important;
  white-space: nowrap !important;
}

table.dataTable thead th {
  background-color: #f8f9fa !important;
  font-weight: 600;
  color: #495057;
}

/* Scroll horizontal suave no modo responsivo */
div.dataTables_wrapper {
  width: 100%;
  overflow-x: auto;
}

/* Ajuste de layout do botão de exportação */
.dt-buttons {
  margin-bottom: 0.5rem;
}

/* Garante prioridade de exibição do modal sobre DataTables */
.modal-backdrop.show { z-index: 1040 !important; }
.modal { z-index: 1050 !important; }

</style>

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">


<script>
$(document).ready(function() {
    let escolaSelecionada = null;

    // Inicializa DataTable
   const tabela = $('#tabelaEscolas').DataTable({
    ajax: '{{ route("escolas.data") }}',
    searching: false, // 🔹 remove a pesquisa nativa
    columns: [
        {
            data: 'id',
            className: 'text-center',
            orderable: false,
            searchable: false,
            render: id => `<input type="radio" name="escolaSelecionada" value="${id}">`
        },
        { data: 'inep', title: 'Inep' },
        { data: 'Escola', title: 'Nome da Escola' },
        { data: 'Municipio', title: 'Município' },
        { data: 'UF', title: 'UF' },
        { data: 'dre_nome', title: 'DRE' }
    ],
    order: [[2, 'asc']],
    pageLength: 10,
    responsive: true,
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json' }
});

$('#tabelaEscolas').on('change', 'input[name="escolaSelecionada"]', function() {
    escolaSelecionada = $(this).val();
    $('#navDetalhes, #navEditar, #navExcluir').removeClass('disabled');
});

// 🔹 Detalhes
$('#navDetalhes').on('click', e => {
    e.preventDefault();
    if (!escolaSelecionada) return;

     fetch(`{{ url("escolas") }}/${escolaSelecionada}`)

        .then(r => r.json())
        .then(({ escola }) => {
            $('#detalhesEscola').html(`
                <tr><th>Código</th><td>${escola.id}</td></tr>
                <tr><th>Nome</th><td>${escola.Escola}</td></tr>
                <tr><th>Município</th><td>${escola.Municipio ?? '-'}</td></tr>
                <tr><th>UF</th><td>${escola.UF ?? '-'}</td></tr>
                <tr><th>INEP</th><td>${escola.inep ?? '-'}</td></tr>
                <tr><th>Telefone</th><td>${escola.Telefone ?? '-'}</td></tr>
                <tr><th>Endereço</th><td>${escola.Endereco ?? '-'}</td></tr>
                <tr><th>DRE</th><td>${escola.dre?.nome_dre ?? '-'}</td></tr>
            `);

            const modalDetalhes = new bootstrap.Modal(document.getElementById('modalDetalhesEscola'));
                modalDetalhes.show();
        })
        .catch(() => alert('Erro ao carregar detalhes.'));
});


    // 🔹 Editar
    $('#navEditar').on('click', function(e) {
        e.preventDefault();
        if (escolaSelecionada)
            window.location.href = `/escolas/${escolaSelecionada}/edit`;
    });

    // 🔹 Excluir
    $('#navExcluir').on('click', function(e) {
        e.preventDefault();
        if (!escolaSelecionada) return;
        if (confirm('Tem certeza que deseja excluir esta escola?')) {
            fetch(`/escolas/${escolaSelecionada}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(resp => {
                if (!resp.ok) throw new Error();
                tabela.ajax.reload();
                alert('Escola excluída com sucesso!');
            })
            .catch(() => alert('Erro ao excluir escola.'));
        }
    });

    // 🔍 Filtros simples (client-side)
    $('#btnAplicarFiltros').on('click', function() {
        tabela.search(
            $('#filtroCodigo').val() + ' ' +
            $('#filtroNome').val() + ' ' +
            $('#filtroMunicipio').val() + ' ' +
            $('#filtroUF').val()
        ).draw();
    });

    $('#btnLimparFiltros').on('click', function() {
        $('#formFiltros input').val('');
        tabela.search('').draw();
    });
});
</script>
@endsection

