@extends('layouts.app')
@section('title', 'Empenhos')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none text-primary fw-semibold">
                    <i class="fas fa-home me-1"></i>Início
                </a>
            </li>
            <li class="breadcrumb-item active text-secondary fw-semibold" aria-current="page">Empenhos</li>
        </ol>
    </nav>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Empenhos
            </h4>
        </div>

        <div class="card-body bg-white">
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
                            <a href="{{ route('empenhos.create') }}" class="btn btn-primary btn-sm px-3">
                                <i class="fas fa-plus-circle me-1"></i> Novo Empenho
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <table id="tabela-empenhos" class="table table-striped no-inner-borders w-100"></table>

        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
.nav-link.disabled { opacity: 0.5; pointer-events: none; }
table.dataTable { border: 1px solid #dee2e6 !important; }
div.dataTables_wrapper { width: 100%; overflow-x: auto; }
</style>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  let selecionado = null;
  const navs = $('#navDetalhes, #navEditar, #navExcluir');

  // 🔹 Inicializa DataTable
  const tabela = $('#tabela-empenhos').DataTable({
    processing: false,
    serverSide: true,
    ajax: "{{ url('/api/empenhos') }}",
    columns: [
      {
        data: 'id',
        title: '',
        orderable: false,
        searchable: false,
        className: 'text-center align-middle',
        render: function (data, type, row) {
          return `
            <input type="radio" name="empenhoSelecionado" value="${data}" class="form-check-input">
          `;
        },
        width: '5%'
      },
      { data: 'numero', title: 'Nº NE' },
      { data: 'empresa', title: 'Empresa' },
      { data: 'contrato', title: 'Contrato' },
      { data: 'data_lancamento', title: 'Data' },
      { data: 'valor_total', title: 'Valor Total' },
      { data: 'acoes', title: 'Ações', orderable: false, searchable: false }
    ],
    language: { url: '/datatables/i18n/pt-BR.json' },
    order: [[1, 'asc']]
  });

  // 🔹 Ao selecionar um radio button
  $('#tabela-empenhos').on('change', 'input[name="empenhoSelecionado"]', function () {
    selecionado = $(this).val();
    navs.removeClass('disabled');
  });

  // 🔹 Botão Detalhes
  $('#navDetalhes').on('click', function (e) {
    e.preventDefault();
    if (!selecionado) return alert('Selecione um empenho primeiro.');
    fetch(`{{ url('empenhos') }}/${selecionado}`)
      .then(r => r.json())
      .then(data => {
        alert('Empenho: ' + (data.empenho?.numero ?? '') + '\nValor: R$ ' + (data.empenho?.valor ?? '0'));
      })
      .catch(() => alert('Erro ao carregar detalhes.'));
  });

  // 🔹 Botão Editar
  $('#navEditar').on('click', function (e) {
    e.preventDefault();
    if (selecionado) window.location.href = `/empenhos/${selecionado}/edit`;
    else alert('Selecione um empenho.');
  });

  // 🔹 Botão Excluir
  $('#navExcluir').on('click', function (e) {
    e.preventDefault();
    if (!selecionado) return alert('Selecione um empenho.');
    if (!confirm('Confirmar exclusão?')) return;

    fetch(`/empenhos/${selecionado}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    })
      .then(resp => {
        if (resp.ok) {
          tabela.ajax.reload();
          navs.addClass('disabled');
          alert('Empenho excluído com sucesso.');
        } else alert('Erro ao excluir.');
      })
      .catch(() => alert('Erro ao excluir.'));
  });
});
</script>
@endsection
