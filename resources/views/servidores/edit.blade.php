@extends('layouts.app')
@section('title','Editar Servidor')

@section('content_body')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-user-edit me-2 text-primary"></i>Editar Servidor</h5>
    </div>
    <form method="POST" action="{{ route('servidores.update', $servidor->id) }}">
      @csrf
      @method('PUT')
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Pessoa</label>
          <select name="pessoa_id" class="form-select" required>
            <option value="">Selecione...</option>
            @foreach ($pessoas as $p)
              <option value="{{ $p->id }}" {{ $servidor->pessoa_id == $p->id ? 'selected' : '' }}>{{ $p->nome_completo }} (CPF: {{ $p->cpf }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Matrícula</label>
          <input type="text" name="matricula" class="form-control" value="{{ old('matricula', $servidor->matricula) }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Data admissão</label>
          <input type="date" name="data_admissao" class="form-control" value="{{ old('data_admissao', optional($servidor->data_admissao)->format('Y-m-d')) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Cargo</label>
          <input type="text" name="cargo" class="form-control" value="{{ old('cargo', $servidor->cargo) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Função</label>
          <input type="text" name="funcao" class="form-control" value="{{ old('funcao', $servidor->funcao) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Lotação</label>
          <input type="text" name="lotacao" class="form-control" value="{{ old('lotacao', $servidor->lotacao) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Vínculo</label>
          <select name="vinculo" class="form-select">
            <option value="">—</option>
            <option value="efetivo" {{ old('vinculo', $servidor->vinculo) == 'efetivo' ? 'selected' : '' }}>Efetivo</option>
            <option value="comissionado" {{ old('vinculo', $servidor->vinculo) == 'comissionado' ? 'selected' : '' }}>Comissionado</option>
            <option value="temporario" {{ old('vinculo', $servidor->vinculo) == 'temporario' ? 'selected' : '' }}>Temporário</option>
            <option value="terceirizado" {{ old('vinculo', $servidor->vinculo) == 'terceirizado' ? 'selected' : '' }}>Terceirizado</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Situação</label>
          <select name="situacao" class="form-select" required>
            <option value="ativo" {{ old('situacao', $servidor->situacao) == 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ old('situacao', $servidor->situacao) == 'inativo' ? 'selected' : '' }}>Inativo</option>
            <option value="afastado" {{ old('situacao', $servidor->situacao) == 'afastado' ? 'selected' : '' }}>Afastado</option>
            <option value="aposentado" {{ old('situacao', $servidor->situacao) == 'aposentado' ? 'selected' : '' }}>Aposentado</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Salário</label>
          <input type="number" step="0.01" name="salario" class="form-control" value="{{ old('salario', $servidor->salario) }}">
        </div>
      </div>
      <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('servidores.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
      </div>
    </form>
  </div>
</div>
@endsection
