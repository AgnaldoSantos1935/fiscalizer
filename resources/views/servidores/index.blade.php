@extends('layouts.app')
@section('title','Servidores')

@section('content_body')
<div class="container-fluid">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0 text-secondary"><i class="fas fa-users me-2 text-primary"></i>Servidores</h4>

    </div>
    <div class="card-body">
      <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3" method="GET" action="{{ route('servidores.index') }}">
        <div class="col-md-3">
          <label class="form-label small text-secondary">Matrícula</label>
          <input type="text" name="matricula" value="{{ request('matricula') }}" class="form-control form-control-sm" placeholder="Ex.: 12345">
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary">Nome</label>
          <input type="text" name="nome" value="{{ request('nome') }}" class="form-control form-control-sm" placeholder="Nome do servidor">
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary">Cargo</label>
          <input type="text" name="cargo" value="{{ request('cargo') }}" class="form-control form-control-sm" placeholder="Cargo">
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary">Lotação</label>
          <input type="text" name="lotacao" value="{{ request('lotacao') }}" class="form-control form-control-sm" placeholder="Lotação">
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary">Vínculo</label>
          <select name="vinculo" class="form-select form-select-sm">
            <option value="">Todos</option>
            @foreach(($vinculos ?? []) as $v)
              <option value="{{ $v }}" @selected(request('vinculo')===$v)>{{ ucfirst($v) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary">Situação</label>
          <select name="situacao" class="form-select form-select-sm">
            <option value="">Todas</option>
            @foreach(($situacoes ?? []) as $s)
              <option value="{{ $s }}" @selected(request('situacao')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary">Admissão (de)</label>
          <input type="date" name="admissao_ini" value="{{ request('admissao_ini') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary">Admissão (até)</label>
          <input type="date" name="admissao_fim" value="{{ request('admissao_fim') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-3 d-flex justify-content-end align-items-end">
          <div class="d-flex w-100">
            <button type="submit" class="btn btn-primary btn-sm btn-sep flex-grow-1"><i class="fas fa-filter me-1"></i> Filtrar</button>
            <a href="{{ route('servidores.index') }}" class="btn btn-outline-secondary btn-sm btn-sep flex-grow-1"><i class="fas fa-undo me-1"></i> Limpar</a>
          </div>
        </div>
      </form>

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
        <tbody>
          @foreach(($servidores ?? []) as $s)
            <tr>
              <td>{{ $s->id }}</td>
              <td>{{ $s->matricula ?? '—' }}</td>
              <td>{{ optional($s->pessoa)->nome_completo ?? '—' }}</td>
              <td>{{ $s->cargo ?? '—' }}</td>
              <td>{{ $s->lotacao ?? '—' }}</td>
              <td>{{ $s->vinculo ?? '—' }}</td>
              <td>{{ ucfirst($s->situacao ?? '') }}</td>
              <td>{{ $s->data_admissao ? $s->data_admissao->format('d/m/Y') : '—' }}</td>
              <td>
                <a href="{{ route('servidores.edit', $s->id) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                <form method="POST" action="{{ route('servidores.destroy', $s->id) }}" class="d-inline" onsubmit="return confirm('Excluir este servidor?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Excluir</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-2">{{ ($servidores ?? null)?->links() }}</div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Nenhum JS de DataTables necessário; ações são nativas
});
</script>
@endpush
