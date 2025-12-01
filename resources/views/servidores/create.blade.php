@extends('layouts.app')
@section('title','Cadastrar Servidor')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-user-plus me-2 text-primary"></i>Cadastrar Servidor</h5>
    </div>
    <form method="POST" action="{{ route('servidores.store') }}">
      @csrf
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Pessoa</label>
          <select name="pessoa_id" class="form-select" required>
            <option value="">Selecione...</option>
            @foreach ($pessoas as $p)
              <option value="{{ $p->id }}">{{ $p->nome_completo }} (CPF: {{ $p->cpf }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Matrícula</label>
          <input type="text" name="matricula" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Data admissão</label>
          <input type="date" name="data_admissao" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Cargo</label>
          <input type="text" name="cargo" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Função</label>
          <input type="text" name="funcao" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Lotação</label>
          <input type="text" name="lotacao" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Vínculo</label>
          <select name="vinculo" class="form-select">
            <option value="">—</option>
            <option value="efetivo">Efetivo</option>
            <option value="comissionado">Comissionado</option>
            <option value="temporario">Temporário</option>
            <option value="terceirizado">Terceirizado</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Situação</label>
          <select name="situacao" class="form-select" required>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
            <option value="afastado">Afastado</option>
            <option value="aposentado">Aposentado</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Salário</label>
          <input type="number" step="0.01" name="salario" class="form-control">
        </div>
      </div>
      <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('servidores.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Cadastrar Servidor</button>
      </div>
    </form>
  </div>
</div>
@endsection
