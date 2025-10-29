@extends('layouts.app')
@section('title', 'Escolas')

@section('content')
<br>

<div class="d-flex justify-content-end mb-3">
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#novaEscolaModal">
    <i class="fas fa-plus-circle"></i> Nova Escola
  </button>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <h2 class="mb-0"><i class="fas fa-school"></i> Escolas Cadastradas</h2>
  </div>

  <div class="card-body">
    <table id="tabelaEscolas" class="table table-bordered table-striped align-middle w-100">
      <thead class="table-light">
        <tr>
          <th>Código</th>
          <th>Nome</th>
          <th>Município</th>
          <th>UF</th>
          <th>Cód. INEP</th>
          <th>Telefone</th>
          <th class="text-center">Ações</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
{{-- Modal Detalhes --}}
<div class="modal fade" id="modalDetalhesEscola" tabindex="-1" aria-labelledby="detalhesEscolaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="detalhesEscolaLabel"><i class="fas fa-eye"></i> Detalhes da Escola</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="detalhesConteudo">
          <div class="text-center py-4">
            <div class="spinner-border text-info" role="status">
              <span class="sr-only">Carregando...</span>
            </div>
            <p class="mt-2">Carregando informações...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
{{-- Modal Nova Escola --}}
<div class="modal fade" id="novaEscolaModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Nova Escola</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{ route('escolas.store') }}" id="formNovaEscola">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Código</label>
              <input type="text" name="codigo" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Nome</label>
              <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
              <label>Município</label>
              <input type="text" name="municipio" class="form-control">
            </div>
            <div class="col-md-2 mb-3">
              <label>UF</label>
              <input type="text" name="uf" maxlength="2" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
              <label>Cód. INEP</label>
              <input type="text" name="codigo_inep" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
              <label>Telefone</label>
              <input type="text" name="telefone" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function () {
  // Inicializa DataTable com carregamento via AJAX
  var tabela = $('#tabelaEscolas').DataTable({
  ajax: '{{ route('escolas.data') }}',
  columns: [
    { data: 'codigo', title: 'Código' },
    { data: 'Escola', title: 'Nome da Escola' },
    { data: 'Municipio', title: 'Município' },
    { data: 'UF', title: 'UF' },
    { data: 'inep', title: 'Cód. INEP' },
    { data: 'Telefone', title: 'Telefone' },
    {
      data: 'codigo',
      title: 'Ações',
      className: 'text-center',
      orderable: false,
      searchable: false,
      render: function (data) {
        return `
          <button class="btn btn-sm btn-info text-white" onclick="verDetalhes(${data})"><i class="fas fa-eye"></i></button>
          <button class="btn btn-sm btn-warning text-white" onclick="editarEscola(${data})"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger" onclick="excluirEscola(${data})"><i class="fas fa-trash"></i></button>
        `;
      }
    }
  ],
  columnDefs: [
    { width: "8%", targets: 0 },  // Código
    { width: "25%", targets: 1 }, // Nome
    { width: "20%", targets: 2 }, // Município
    { width: "5%",  targets: 3 }, // UF
    { width: "15%", targets: 4 }, // INEP
    { width: "12%", targets: 5 }, // Telefone
    { width: "15%", targets: 6 }  // Ações
  ],
  autoWidth: false,
  responsive: true,
  pageLength: 10,
  dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
  buttons: [
    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm' },
    { extend: 'pdfHtml5',   text: '<i class="fas fa-file-pdf"></i> PDF',   className: 'btn btn-danger btn-sm' },
    { extend: 'csvHtml5',   text: '<i class="fas fa-file-csv"></i> CSV',   className: 'btn btn-secondary btn-sm' },
    { extend: 'print',      text: '<i class="fas fa-print"></i> Imprimir', className: 'btn btn-dark btn-sm' }
  ],
  language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json' }
});

  // Submissão do formulário de nova escola via AJAX
  $('#formNovaEscola').submit(function (e) {
    e.preventDefault();

    $.ajax({
      url: '{{ route('escolas.store') }}',
      method: 'POST',
      data: $(this).serialize(),
      success: function (res) {
        $('#novaEscolaModal').modal('hide');
        tabela.ajax.reload(null, false);
        alert('✅ Escola cadastrada com sucesso!');
      },
      error: function (xhr) {
        alert('❌ Erro ao cadastrar: ' + xhr.responseJSON.message);
      }
    });
  });
});

// Funções para ações (a serem expandidas)

function verDetalhes(codigo) {
  $('#modalDetalhesEscola').modal('show');
  $('#detalhesConteudo').html(`
    <div class="text-center py-4">
      <div class="spinner-border text-info" role="status"></div>
      <p class="mt-2">Carregando informações...</p>
    </div>
  `);

  $.ajax({
    url: '/escolas/${codigo}/detalhes',
    type: 'GET',
    success: function (data) {
      $('#detalhesConteudo').html(`
        <p><strong>Código:</strong> ${data.codigo ?? '-'}</p>
        <p><strong>Nome:</strong> ${data.Escola ?? '-'}</p>
        <p><strong>Município:</strong> ${data.Municipio ?? '-'}</p>
        <p><strong>UF:</strong> ${data.UF ?? '-'}</p>
        <p><strong>Cód. INEP:</strong> ${data.inep ?? '-'}</p>
        <p><strong>Telefone:</strong> ${data.Telefone ?? '-'}</p>
      `);
    },
    error: function () {
      $('#detalhesConteudo').html('<p class="text-danger">Erro ao carregar os detalhes da escola.</p>');
    }
  });
}


function editarEscola(codigo) {
  alert('Abrir modal de edição para escola ID: ' + codigo);
}

function excluirEscola(codigo) {
  if (confirm('Tem certeza que deseja excluir esta escola?')) {
    $.ajax({
      url: '/escolas/' + codigo,
      type: 'DELETE',
      data: { _token: '{{ csrf_token() }}' },
      success: function () {
        $('#tabelaEscolas').DataTable().ajax.reload(null, false);
        alert('🗑️ Escola excluída com sucesso!');
      }
    });
  }
}
</script>

<style>
.modal-backdrop.show { z-index: 1040 !important; }
.modal { z-index: 1050 !important; }
</style>
@endsection
