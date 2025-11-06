@extends('layouts.app')
@php
use Illuminate\Support\Str;
@endphp

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

<form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3">
  <div class="col-md-3">


    <label for="filtroNumero" class="form-label fw-semibold text-secondary small">Número</label>
    <input type="text" id="filtroNumero" name="numero"
           value="{{ request('numero') }}" class="form-control form-control-sm" type="text"
           placeholder="Ex: 065/2025">
  </div>

  <div class="col-md-4">
    <label for="filtroEmpresa" class="form-label fw-semibold text-secondary small">Empresa</label>
    <input type="text" id="filtroEmpresa" name="empresa"
           value="{{ request('empresa') }}" class="form-control form-control-sm" type="text"
           placeholder="Digite parte do nome">
  </div>

  <div class="col-md-3">
    <label for="filtroSituacao" class="form-label fw-semibold text-secondary small">Situação</label>
    <select id="filtroSituacao" name="situacao" class="custom-select form-control-border">
  <option value="">Todas</option>
</select>
  </div>

<div class="col-md-2 d-flex justify-content-end align-items-end">
  <div class="d-flex w-100">
    <button type="button" id="btnAplicarFiltros" class="btn btn-primary btn-sm btn-sep flex-grow-1">
      <i class="fas fa-filter me-1"></i> Filtrar
    </button>
    <button type="button" id="btnLimpar" class="btn btn-outline-secondary btn-sm btn-sep flex-grow-1">
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
              <!-- 🔹 Navbar de ações -->
             <nav class="nav nav-pills flex-column flex-sm-row">

    <ul class="nav nav-pills">
      <li class="nav-item">
        <a id="navDetalhes" class="nav-link inative" aria-current="page" href="#">
          <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('contratos.create') }}" class="nav-link active" aria-current="page">
          <i class="fas fa-plus-circle me-1"></i> Novo Contrato
        </a>
      </li>
    </ul>

</nav>
<br>
<!--Legendas de situação dos contratos-->
<div id="legendaSituacoes" class="mb-3 d-flex flex-wrap align-items-center gap-2 small text-secondary">
  <i class="fas fa-info-circle me-2 text-primary"></i>
  <span> Legenda de Situações: </span>
  <div id="listaLegendas" class="d-flex flex-wrap gap-2 ms-2"></div>
</div>
<!---->
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
      <th>Situação</th>
    </tr>
  </thead>
  <tbody>

  </tbody>
</table>



        </div>
    </div>
</div>

<!-- Modal de Itens Contratados -->
<!--<div class="modal fade" id="modalItensContrato" tabindex="-1" aria-hidden="true">
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
</div> -->

@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
   .custom-tooltip {
  --bs-tooltip-bg: var(--bd-violet-bg);
  --bs-tooltip-color: var(--bs-white);
}
   #legendaSituacoes {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 8px 12px;
}

#listaLegendas .badge {
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.3px;
  cursor: help;
}

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
 #formFiltros .btn-sep + .btn-sep { margin-left: .5rem !important; }

</style>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
$(document).ready(function () {

// 🔹 Monta o filtro e a legenda com cores, ícones e tooltips
fetch('{{ route('api.situacoes') }}')
  .then(resp => resp.json())
  .then(situacoes => {
      const select = $('#filtroSituacao');
      const legenda = $('#listaLegendas');

      select.empty().append('<option value="">Todas</option>');
      legenda.empty();

      situacoes.forEach(s => {
          // define cor + ícone
          const map = badgeClassFromColor(s.cor);
          const descricao = s.descricao ? s.descricao.replace(/"/g, '&quot;') : 'Sem descrição.';

          // adiciona no filtro
          select.append(`<option value="${s.slug}">${s.nome}</option>`);

          // adiciona badge com ícone + tooltip
          legenda.append(`
              <span class="${map.cls} px-3 py-2 shadow-sm d-flex align-items-center gap-1"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="${descricao}">
                  <i class="fas ${map.icon}"></i> ${s.nome}
              </span>
          `);
      });

      // inicializa tooltips do Bootstrap
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
  })
  .catch(err => console.error('Erro ao carregar situações:', err));

    // 🔹 Carrega as situações da API e popula o select
fetch ('{{ route('api.situacoes') }}')
  .then(resp => resp.json())
  .then(situacoes => {
      const select = $('#filtroSituacao');
      select.empty().append('<option value="">Todas</option>');
      situacoes.forEach(s => {
          // Exibe o nome da situação
          select.append(`
              <option>
                  ${s.nome}
              </option>
          `);
      });
  })
  .catch(err => console.error('Erro ao carregar situações:', err));

    // ==============================
    // 🔹 Inicializa DataTable via AJAX
    // ==============================
    const tabela = $('#tabelaContratos').DataTable({
        ajax: '{{ route('api.contratos') }}',
        language: { url: '{{ asset('js/pt-BR.json') }}' },
        pageLength: 10,
        order: [[1, 'asc']],
        dom: 't<"bottom"p>',
        responsive: true,
        columns: [
            {
                data: null,
                className: 'text-center',
                render: (d) => `<input type="radio" name="contratoSelecionado" value="${d.id}">`
            },
            { data: 'numero', defaultContent: '—' },
            { data: 'objeto', render: (d) => d ? d.substring(0, 80) + (d.length > 80 ? '…' : '') : '—' },
            { data: 'contratada.razao_social', defaultContent: '—' },
            {
                data: 'valor_global',
                render: (v) => v ? 'R$ ' + parseFloat(v).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '—'
            },
            {
                data: 'data_inicio',
                render: (d) => d ? new Date(d).toLocaleDateString('pt-BR') : '—'
            },
          {
  data: 'situacao_contrato',
  render: function (s) {
    if (!s) return '-';
    const cls = badgeClassFromColor(s.cor);
    return `<span class="${cls}">${s.nome}</span>`;
  }
}
        ]
    });

    let contratoSelecionado = null;



    // =====================================================
    // Marque um radio e ative “Detalhes”
    // =====================================================

    $('#tabelaContratos').on('change', 'input[name="contratoSelecionado"]', function () {
        contratoSelecionado = $(this).val();
        $('#navDetalhes').removeClass('disabled');
    });


    // =====================================================
    // 🔹 Botão “Detalhes” → redireciona para a view do contrato
    // =====================================================
    $('#navDetalhes').on('click', function (e) {
        e.preventDefault();
        if (!contratoSelecionado) return;
        window.location.href = '{{ url("contratos") }}/' + contratoSelecionado;
    });

    // ===================================
    // 🔍 Aplicar filtros (funciona igual à versão antiga)
    // ===================================
    $('#btnAplicarFiltros').on('click', function (e) {
        e.preventDefault();

        const numero   = $('#filtroNumero').val().trim().toLowerCase();
        const empresa  = $('#filtroEmpresa').val().trim().toLowerCase();
        const situacao = $('#filtroSituacao').val().trim().toLowerCase();

        // Atualiza os filtros nas colunas
        tabela.column(1).search(numero);
        tabela.column(3).search(empresa);
        tabela.draw();

        // Filtro de situação (porque é HTML)
        $('#tabelaContratos tbody tr').each(function () {
            const badgeText = $(this).find('td:nth-child(7) span').text().trim().toLowerCase();
            const match = !situacao || badgeText.includes(situacao);
            $(this).toggle(match);
        });
    });
$('#filtroSituacao').on('change', function () {
    const cor = $(this).find(':selected').data('cor');
    if (cor) {
        $(this).removeClass().addClass('form-select form-select-sm text-center bg-' + cor + ' text-white');
    } else {
        $(this).removeClass().addClass('form-select form-select-sm text-center');
    }
});

    // ===========================================
    // 🔄 Limpar filtros → reseta e recarrega AJAX
    // ===========================================
    $('#btnLimpar').on('click', function (e) {
        e.preventDefault();
        $('#formFiltros')[0].reset();

        tabela.search('');
        tabela.columns().search('');
        tabela.order([1, 'asc']);
        tabela.ajax.reload(null, false);

        $('input[name="contratoSelecionado"]').prop('checked', false);
        contratoSelecionado = null;
        $('#navDetalhes').addClass('disabled');
    });


    function badgeClassFromColor(cor) {
  if (!cor) return 'badge bg-secondary';
  const slug = cor.normalize('NFD')
                  .replace(/\p{Diacritic}/gu, '')
                  .toLowerCase()
                  .replace(/[^a-z]/g, '');
  const map = {
    azul: 'primary',
    verde: 'success',
    amarelo: 'warning',
    vermelho: 'danger',
    cinza: 'secondary',
    preto: 'dark',
    branco: 'light',
    ciano: 'info',
    roxo: 'secondary',
    laranja: 'warning',
  };
  const cls = map[slug] || 'secondary';
  const needsDark = cls === 'warning' || cls === 'light';
  return `badge bg-${cls}${needsDark ? ' text-dark' : ''}`;
}


});
</script>



@endsection
