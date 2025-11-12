@extends('layouts.app')
@section('title', 'Notas de Empenho')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Notas de Empenho</h4>
      <a href="{{ route('empenhos.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Novo Empenho</a>
    </div>

    <div class="card-body">
      <table id="tabelaEmpenhos" class="table table-hover w-100">
        <thead>
          <tr>
            <th>Número</th>
            <th>Empresa</th>
            <th>Contrato</th>
            <th>Data</th>
            <th>Valor Total</th>
            <th width="120">Ações</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
$(function() {
  $('#tabelaEmpenhos').DataTable({
    ajax: '{{ route('empenhos.data') }}',
     language: { url: '{{ asset(`js/pt-BR.json`) }}' },
        pageLength: 10,
        order: [[1, 'asc']],
          processing: false,
    serverSide: true,
        dom: 't<"bottom"p>',
        responsive: true,
    columns: [
      { data: 'numero', name: 'numero' },
      { data: 'empresa', name: 'empresa.razao_social' },
      { data: 'contrato', name: 'contrato.numero' },
      { data: 'data_lancamento', name: 'data_lancamento' },
      { data: 'valor_total', name: 'valor_total', className: 'text-end fw-semibold' },
      { data: 'acoes', orderable: false, searchable: false, className: 'text-end' }
    ]
  });
});
</script>
@endsection
