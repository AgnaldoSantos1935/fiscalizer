@extends('layouts.app')
@section('title','Servidores')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0 text-secondary"><i class="fas fa-users me-2 text-primary"></i>Servidores</h4>
      <a href="{{ route('servidores.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Novo Servidor
      </a>
    </div>
    <div class="card-body">
      <table id="tabelaServidores" class="table table-striped table-hover w-100">
        <thead>
          <tr>
            <th>ID</th>
            <th>Matrícula</th>
            <th>Nome</th>
            <th>Cargo</th>
            <th>Lotação</th>
            <th>Vínculo</th>
            <th>Situação</th>
            <th>Admissão</th>
            <th>Ações</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  $('#tabelaServidores').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route('api.servidores.index') }}',
    columns: [
      { data: 'id', name: 'id' },
      { data: 'matricula', name: 'matricula' },
      { data: 'nome', name: 'pessoa.nome_completo', orderable: true, searchable: true },
      { data: 'cargo', name: 'cargo' },
      { data: 'lotacao', name: 'lotacao' },
      { data: 'vinculo', name: 'vinculo' },
      { data: 'situacao', name: 'situacao' },
      { data: 'data_admissao', name: 'data_admissao' },
      { data: 'acoes', orderable: false, searchable: false }
    ],
    language: { url: '{{ asset("js/pt-BR.json") }}' },
    dom: 't<"bottom"ip>'
  });
});
</script>
@endpush
