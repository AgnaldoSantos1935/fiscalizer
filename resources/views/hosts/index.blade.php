@extends('layouts.app')
@section('title', 'Hosts Cadastrados')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h4 class="text-secondary fw-semibold mb-0">
        <i class="fas fa-server text-primary me-2"></i> Hosts Cadastrados
      </h4>
      <a href="{{ route('hosts.create') }}" class="btn btn-success btn-sm">
        <i class="fas fa-plus"></i> Novo Host
      </a>
    </div>

    <div class="card-body">
      @if($hosts->isEmpty())
        <div class="alert alert-light text-center text-muted">Nenhum host cadastrado.</div>
      @else
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Nome</th>
              <th>Endereço</th>
              <th>Tipo</th>
              <th>Localização</th>
              <th>Ativo</th>
              <th class="text-center">Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($hosts as $h)
            <tr>
              <td>{{ $h->nome }}</td>
              <td>{{ $h->endereco }}</td>
              <td>{{ strtoupper($h->tipo) }}</td>
              <td>{{ $h->localizacao ?? '—' }}</td>
              <td>
                {!! $h->ativo
                    ? '<span class="badge bg-success">Ativo</span>'
                    : '<span class="badge bg-secondary">Inativo</span>' !!}
              </td>
              <td class="text-center">
                <a href="{{ route('hosts.show', $h->id) }}" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></a>
                <a href="{{ route('hosts.edit', $h->id) }}" class="btn btn-outline-warning btn-sm"><i class="fas fa-edit"></i></a>
                <form action="{{ route('hosts.destroy', $h->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Excluir este host?')">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
