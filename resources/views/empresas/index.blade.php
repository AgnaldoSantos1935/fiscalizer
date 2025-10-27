@extends('layouts.app')
@section('title', 'Empresas')

@section('content')
<br>

<!-- Botão acima do card -->
<div class="d-flex justify-content-end mb-3">


  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#novaEmpresaModal">
    <i class="fas fa-plus-circle"></i> Empresa
  </button>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <h2 class="mb-0"><i class="fas fa-building"></i> Empresas Cadastradas</h2>
  </div>

  <div class="card-body">
    <table id="tabelaEmpresas" class="table table-bordered table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>Razão Social</th>
          <th>CNPJ</th>
          <th>Email</th>
          <th>Telefone</th>
          <th class="text-center" style="width: 150px;">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($empresas as $empresa)
          <tr>
            <td>{{ $empresa->razao_social }}</td>
            <td>{{ $empresa->cnpj }}</td>
            <td>{{ $empresa->email }}</td>
            <td>{{ $empresa->telefone }}</td>
            <td class="text-center">
  <button class="btn btn-sm btn-info text-white"
        data-toggle="modal"
        data-target="#detalhesModal{{ $empresa->id }}">
  <i class="fas fa-eye"></i>
</button>

<button type="button" class="btn btn-sm btn-warning text-white" data-toggle="modal" data-target="#editarModal{{ $empresa->id }}">
    <i class="fas fa-edit"></i></button>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">Nenhuma empresa cadastrada.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Modais (fora da tabela) --}}
@foreach ($empresas as $empresa)
  {{-- Modal de detalhes --}}
  <div class="modal fade" id="detalhesModal{{ $empresa->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title"><i class="fas fa-eye"></i> Detalhes da Empresa</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
          <p><strong>Razão Social:</strong> {{ $empresa->razao_social }}</p>
          <p><strong>CNPJ:</strong> {{ $empresa->cnpj }}</p>
          <p><strong>Email:</strong> {{ $empresa->email }}</p>
          <p><strong>Telefone:</strong> {{ $empresa->telefone }}</p>
          <p><strong>Endereço:</strong> {{ $empresa->endereco ?? '-' }}</p>
          <p><strong>Cidade:</strong> {{ $empresa->cidade ?? '-' }}</p>
          <p><strong>UF:</strong> {{ $empresa->uf ?? '-' }}</p>
          <p><strong>CEP:</strong> {{ $empresa->cep ?? '-' }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>

        </div>
      </div>
    </div>
  </div>

  {{-- Modal de edição --}}
  <div class="modal fade" id="editarModal{{ $empresa->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Empresa</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <form method="POST" action="{{ route('empresas.update', $empresa->id) }}">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Razão Social</label>
                <input type="text" name="razao_social" class="form-control" value="{{ $empresa->razao_social }}">
              </div>
              <div class="col-md-6 mb-3">
                <label>CNPJ</label>
                <input type="text" name="cnpj" class="form-control" value="{{ $empresa->cnpj }}">
              </div>
              <div class="col-md-6 mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $empresa->email }}">
              </div>
              <div class="col-md-6 mb-3">
                <label>Telefone</label>
                <input type="text" name="telefone" class="form-control" value="{{ $empresa->telefone }}">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning text-white">Salvar Alterações</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach

{{-- Modal Nova Empresa --}}
<div class="modal fade" id="novaEmpresaModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Nova Empresa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{ route('empresas.store') }}">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Razão Social</label>
              <input type="text" name="razao_social" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>CNPJ</label>
              <input type="text" name="cnpj" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label>Telefone</label>
              <input type="text" name="telefone" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
  // Evita reinit: destrói se já existir, depois inicializa 1 vez
  function initEmpresasDT() {
    var sel = '#tabelaEmpresas';
    if ($.fn.DataTable.isDataTable(sel)) {
      $(sel).DataTable().destroy();
    }
    $(sel).DataTable({
      responsive: true,
      pageLength: 10,
      dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
      buttons: [
        { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm' },
        { extend: 'pdfHtml5',   text: '<i class="fas fa-file-pdf"></i> PDF',   className: 'btn btn-danger btn-sm' },
        { extend: 'csvHtml5',   text: '<i class="fas fa-file-csv"></i> CSV',   className: 'btn btn-secondary btn-sm' },
        { extend: 'print',      text: '<i class="fas fa-print"></i> Imprimir', className: 'btn btn-dark btn-sm' }
      ],
      language: { url: '../datatables/i18n/pt-BR.json' }
    });
  }

  $(document).ready(function() {
    initEmpresasDT();
  });
</script>

<style>
/* Garante que o modal fique acima do overlay da sidebar */
.modal-backdrop.show { z-index: 1040 !important; }
.modal { z-index: 1050 !important; }
</style>
@endsection
