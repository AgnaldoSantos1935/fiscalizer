@extends('layouts.app')
@section('title','Boletins de Medição')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Boletins de Medição</h5>
      <a href="{{ route('boletins.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Novo</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="tabelaBoletins" class="table table-hover align-middle w-100">
          <thead class="table-light">
            <tr>
              <th>Projeto</th>
              <th>Mês</th>
              <th>Valor Total</th>
              <th class="text-center">Ações</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
$(function(){
  $('#tabelaBoletins').DataTable({
    ajax: {
      url: '{{ route('boletins.index') }}',
      dataSrc: 'data'
    },
    language: { url: '{{ asset('js/pt-BR.json') }}' },
    dom: 't<"bottom"ip>',
    columns: [
      { data: 'projeto' },
      { data: 'mes' },
      { data: 'valor_total' },
      { data: 'acoes', orderable: false, searchable: false }
    ]
  });
});
</script>
@endsection