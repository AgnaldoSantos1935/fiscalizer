@extends('layouts.app')
@section('title','Situações de Contrato')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0">
      <h5 class="mb-0 text-secondary fw-semibold"><i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa</h5>
    </div>
    <div class="card-body bg-white">
      <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4" method="GET" action="{{ route('situacoes.index') }}">
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-secondary">Nome</label>
          <input type="text" name="nome" value="{{ request('nome') }}" class="form-control form-control-sm" placeholder="Ex.: vigente, suspenso...">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-secondary">Slug</label>
          <input type="text" name="slug" value="{{ request('slug') }}" class="form-control form-control-sm" placeholder="ex.: vigente">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-secondary">Cor</label>
          <input type="text" name="cor" value="{{ request('cor') }}" class="form-control form-control-sm" placeholder="ex.: bg-success">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-secondary">Motivo</label>
          <input type="text" name="motivo" value="{{ request('motivo') }}" class="form-control form-control-sm" placeholder="Ex.: ajuste, suspensão...">
        </div>
        <div class="col-md-3 d-flex justify-content-end align-items-end">
          <div class="d-flex w-100">
            <button type="submit" id="btnAplicarFiltros" class="btn btn-primary btn-sm btn-sep flex-grow-1">
              <i class="fas fa-filter me-1"></i> Filtrar
            </button>
            <a href="{{ route('situacoes.index') }}" id="btnLimparFiltros" class="btn btn-outline-secondary btn-sm btn-sep flex-grow-1">
              <i class="fas fa-undo me-1"></i> Limpar
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
      <h4 class="mb-0 text-secondary fw-semibold"><i class="fas fa-tags me-2 text-primary"></i>Situações de Contrato</h4>

    </div>
    <div class="card-body bg-white">
      <div class="table-responsive">
        <table id="tabelaSituacoes" class="table table-hover align-middle w-100">
          <thead class="table-light">
            <tr>
              <th class="text-nowrap" style="width:70px">ID</th>
              <th>Nome</th>
              <th>Descrição</th>
              <th class="text-nowrap">Slug</th>
              <th class="text-nowrap">Cor</th>
              <th>Motivo</th>
              <th class="text-nowrap" style="width: 160px">Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach(($situacoes ?? []) as $s)
              <tr>
                <td class="text-nowrap">{{ $s->id }}</td>
                <td>{{ $s->nome }}</td>
                <td class="text-muted">{{ $s->descricao ?? '—' }}</td>
                <td class="text-nowrap">{{ $s->slug ?? '—' }}</td>
                <td class="text-nowrap">{{ $s->cor ?? '—' }}</td>
                <td class="text-muted">{{ $s->motivo ?? '—' }}</td>
                <td class="text-nowrap">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ url('situacoes') }}/{{ $s->id }}">Ver</a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ url('situacoes') }}/{{ $s->id }}/edit">Editar</a>
                  <form action="{{ url('situacoes') }}/{{ $s->id }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja excluir esta situação?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div class="mt-2">{{ ($situacoes ?? null)?->links() }}</div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('js')
<script>
$(function(){ /* sem DataTables */ });
</script>
@endpush
