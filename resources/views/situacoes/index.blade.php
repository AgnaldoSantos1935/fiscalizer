@extends('layouts.app')
@section('title','Situações de Contrato')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
      <h4 class="mb-0 text-secondary fw-semibold"><i class="fas fa-tags me-2 text-primary"></i>Situações de Contrato</h4>
      <div>
        <a href="{{ route('situacoes.create') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus-circle me-1"></i>Nova Situação</a>
      </div>
    </div>
    <div class="card-body bg-white">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th class="text-nowrap">ID</th>
              <th>Nome</th>
              <th>Descrição</th>
              <th class="text-nowrap">Slug</th>
              <th class="text-nowrap">Cor</th>
              <th class="text-nowrap" style="width: 160px">Ações</th>
            </tr>
          </thead>
          <tbody>
            @forelse($situacoes as $s)
            <tr>
              <td>{{ $s->id }}</td>
              <td>{{ $s->nome }}</td>
              <td class="text-muted">{{ $s->descricao ?? '—' }}</td>
              <td><span class="badge bg-secondary">{{ $s->slug }}</span></td>
              <td>{{ $s->cor ?? '—' }}</td>
              <td>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('situacoes.show', $s->id) }}">Ver</a>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('situacoes.edit', $s->id) }}">Editar</a>
                <form action="{{ route('situacoes.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja excluir esta situação?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Excluir</button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Nenhuma situação cadastrada.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
