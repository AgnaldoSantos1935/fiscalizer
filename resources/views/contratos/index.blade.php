@extends('layouts.app')

@section('title', 'Contratos')

@section('content')
<div class="container-fluid">
  <!-- 🔹 Card de Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
            </h4>
        </div>

        <div class="card-body bg-white">
                <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end">

    <div class="col-md-3">
        <label for="filtroNumero" class="form-label fw-semibold text-secondary small">Número do Contrato</label>
        <input type="text" id="filtroNumero" class="form-control form-control-sm" placeholder="Ex: 065/2025">
    </div>

    <div class="col-md-4">
        <label for="filtroEmpresa" class="form-label fw-semibold text-secondary small">Empresa Contratada</label>
        <input type="text" id="filtroEmpresa" class="form-control form-control-sm" placeholder="Digite parte do nome">
    </div>

    <div class="col-md-3">
        <label for="filtroSituacao" class="form-label fw-semibold text-secondary small">Situação</label>
        <select id="filtroSituacao" class="form-select form-select-sm">
            <option value="">Todas</option>
            <option value="vigente">Vigente</option>
            <option value="encerrado">Encerrado</option>
            <option value="suspenso">Suspenso</option>
        </select>
    </div>

    <!-- ✅ Botões perfeitamente alinhados -->
    <div class="col-md-2 d-flex justify-content-end align-items-end">
        <div class="btn-group w-100">
            <button type="button" id="btnAplicarFiltros" class="btn btn-primary btn-sm px-3 me-2 flex-fill">
                <i class="fas fa-filter me-1"></i> Filtrar
            </button>
            <button type="button" id="btnLimparFiltros" class="btn btn-outline-secondary btn-sm px-3 flex-fill">
                <i class="fas fa-undo me-1"></i> Limpar
            </button>
        </div>
    </div>

</form>

        </div>
    </div>

    <!-- 🔹 Card Principal -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-file-contract me-2 text-primary"></i>Contratos Cadastrados
            </h4>
        </div>

        <div class="card-body bg-white">
            <!-- 🔹 Navbar de Ações -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 mb-4 shadow-sm">
                <div class="container-fluid">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item me-3">
                            <a id="navItens" class="nav-link text-dark fw-semibold disabled" href="#">
                                <i class="fas fa-list text-primary me-1"></i> Itens Contratados
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
                            <a href="{{ route('contratos.create') }}" class="btn btn-primary btn-sm px-3">
                                <i class="fas fa-plus-circle me-1"></i> Novo Contrato
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- 🔹 Tabela -->
            <table id="tabelaContratos" class="table table-striped no-inner-borders w-100">
                <thead class="bg-light text-secondary border-bottom">
                    <tr>
                        <th class="text-center" style="width: 45px;">#</th>
                        <th>Número</th>
                        <th>Objeto</th>
                        <th>Empresa Contratada</th>
                        <th>Valor Global (R$)</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contratos as $contrato)
                    <tr class="hoverable-row">
                        <td class="text-center">
                            <input type="radio" name="contratoSelecionado" value="{{ $contrato->id }}">
                        </td>
                        <td>{{ $contrato->numero }}</td>
                        <td>{{ Str::limit($contrato->objeto, 50) }}</td>
                        <td>{{ $contrato->contratada->razao_social ?? '-' }}</td>
                        <td>{{ number_format($contrato->valor_global, 2, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($contrato->data_fim)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $contrato->situacao === 'vigente' ? 'success' : ($contrato->situacao === 'encerrado' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($contrato->situacao) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Itens Contratados -->
<div class="modal fade" id="modalItensContrato" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-boxes"></i> Itens Contratados</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped" id="tabelaItensContrato">
          <thead>
            <tr>
              <th>Descrição</th>
              <th>Unidade</th>
              <th>Quantidade</th>
              <th>Valor Unitário</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody id="listaItensContrato"></tbody>
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
.nav-link {
    transition: color 0.2s ease-in-out;
}
.nav-link:hover {
    color: #0d6efd !important;
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

<script>
$(function() {
    const tabela = $('#tabelaContratos').DataTable({
        language: { url: '/datatables/pt-BR.json' },
        pageLength: 10,
        order: [[1, 'asc']],
        dom: 't<"bottom"p>',
        responsive: true
    });

    let contratoSelecionado = null;

    // Seleção
    $('#tabelaContratos').on('change', 'input[name="contratoSelecionado"]', function() {
        contratoSelecionado = $(this).val();
        $('#navItens, #navEditar, #navExcluir').removeClass('disabled');
    });

    // 🔹 Modal de Itens via AJAX
    $('#navItens').on('click', function(e) {
        e.preventDefault();
        if (!contratoSelecionado) return;

        $('#listaItensContrato').html('<tr><td colspan="5" class="text-center text-muted">Carregando...</td></tr>');

        fetch(`/contratos/${contratoSelecionado}/itens`)
            .then(res => res.json())
            .then(data => {
                if (!data.itens || !data.itens.length) {
                    $('#listaItensContrato').html('<tr><td colspan="5" class="text-center text-muted">Nenhum item vinculado.</td></tr>');
                    return;
                }

                const formatCurrency = (v) => {
                    if (typeof v === 'number') {
                        return v.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                    }
                    if (typeof v === 'string') {
                        const n = Number(v.replace(/\./g, '').replace(',', '.'));
                        return isNaN(n) ? v : n.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                    }
                    return '0,00';
                };

                let linhas = '';
                data.itens.forEach((item) => {
                    linhas += `
                        <tr>
                            <td>${item.descricao_item ?? ''}</td>
                            <td>${item.unidade_medida ?? ''}</td>
                            <td>${item.quantidade ?? 0}</td>
                            <td>${formatCurrency(item.valor_unitario)}</td>
                            <td>${formatCurrency(item.valor_total)}</td>
                        </tr>
                    `;
                });
                $('#listaItensContrato').html(linhas);
            })
            .catch(() => {
                $('#listaItensContrato').html('<tr><td colspan="5" class="text-center text-danger">Erro ao carregar itens.</td></tr>');
            });

        new bootstrap.Modal(document.getElementById('modalItensContrato')).show();
    });

    // Filtros
    $('#btnAplicarFiltros').on('click', function() {
        const numero = $('#filtroNumero').val().toLowerCase();
        const empresa = $('#filtroEmpresa').val().toLowerCase();
        const situacao = $('#filtroSituacao').val().toLowerCase();

        $('#tabelaContratos tbody tr').each(function() {
            const colNumero = $(this).find('td:nth-child(2)').text().toLowerCase();
            const colEmpresa = $(this).find('td:nth-child(4)').text().toLowerCase();
            const colSituacao = $(this).find('td:nth-child(8)').text().toLowerCase();

            $(this).toggle(
                (!numero || colNumero.includes(numero)) &&
                (!empresa || colEmpresa.includes(empresa)) &&
                (!situacao || colSituacao.includes(situacao))
            );
        });
    });

    $('#btnLimparFiltros').on('click', function() {
        $('#formFiltros')[0].reset();
        $('#tabelaContratos tbody tr').show();
    });
});
</script>
@endsection
