@extends('layouts.app')
@section('title', 'Notas de Empenho')

@section('content')
<div class="container-fluid">
  <!-- 🔹 Filtros -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
      <h4 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
      </h4>
    </div>
    <div class="card-body bg-white">
      <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small">Número</label>
          <input type="text" id="filtroNumero" class="form-control form-control-sm" placeholder="Ex: 2023/0001">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small">Empresa</label>
          <input type="text" id="filtroEmpresa" class="form-control form-control-sm" placeholder="Razão Social">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small">Contrato</label>
          <input type="text" id="filtroContrato" class="form-control form-control-sm" placeholder="Número do contrato">
        </div>
        <div class="col-md-3 d-flex justify-content-end align-items-end">
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

  <!-- 🔹 Lista -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Notas de Empenho</h4>
      <a href="{{ route('empenhos.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Novo Empenho</a>
    </div>

    <div class="card-body">
      <!-- 🔹 Navbar de ações -->
      <nav class="nav nav-pills mb-3">
        <ul class="nav nav-pills">
          <li class="nav-item">
            <a id="navDetalhes" class="nav-link disabled" href="#">
              <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
            </a>
          </li>
        </ul>
      </nav>

      <table id="tabelaEmpenhos" class="table table-striped no-inner-borders w-100">
        <thead>
          <tr>
            <th class="text-center" style="width: 45px;"></th>
            <th>Número</th>
            <th>Empresa</th>
            <th>Contrato</th>
            <th>Data</th>
            <th class="text-end">Valor Total</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
  const tabela = $('#tabelaEmpenhos').DataTable({
    ajax: `{{ route('empenhos.data') }}`,
    language: { url: '{{ asset("js/pt-BR.json") }}' },
    pageLength: 10,
    order: [[1, 'asc']],
    dom: 't<"bottom"p>',
    responsive: true,
    columns: [
      {
        data: 'id', orderable: false, className: 'text-center',
        render: (id) => `<input type="radio" name="empenhoSelecionado" value="${id}">`
      },
      { data: 'numero', name: 'numero' },
      { data: 'empresa', name: 'empresa.razao_social' },
      { data: 'contrato', name: 'contrato.numero' },
      { data: 'data_lancamento', name: 'data_lancamento' },
      { data: 'valor_total', name: 'valor_total', className: 'text-end fw-semibold' }
    ]
  });

  let empenhoSelecionado = null;
  $('#tabelaEmpenhos').on('change', 'input[name="empenhoSelecionado"]', function () {
    empenhoSelecionado = $(this).val();
    $('#navDetalhes').removeClass('disabled');
  });
  $('#navDetalhes').on('click', function (e) {
    e.preventDefault();
    if (!empenhoSelecionado) return;
    window.location.href = '{{ url('empenhos') }}' + '/' + empenhoSelecionado;
  });

  $('#btnAplicarFiltros').on('click', function () {
    tabela.column(1).search($('#filtroNumero').val());
    tabela.column(2).search($('#filtroEmpresa').val());
    tabela.column(3).search($('#filtroContrato').val());
    tabela.draw();
  });

  $('#btnLimpar').on('click', function () {
    $('#formFiltros')[0].reset();
    tabela.search('').columns().search('');
    tabela.order([1, 'asc']);
    tabela.ajax.reload(null, false);
    $('#navDetalhes').addClass('disabled');
    empenhoSelecionado = null;
  });
});
</script>
@endsection
