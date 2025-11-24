@extends('layouts.app')
@section('title','Editar Situação de Contrato')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar Situação</h5>
    </div>
    <form method="POST" action="{{ route('situacoes.update', $situacao->id) }}">
      @csrf
      @method('PUT')
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome</label>
          <input type="text" name="nome" class="form-control" value="{{ old('nome', $situacao->nome) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Cor</label>
          <input type="text" name="cor" class="form-control" value="{{ old('cor', $situacao->cor) }}">
        </div>
        <div class="col-md-12">
          <label class="form-label">Descrição</label>
          <textarea name="descricao" class="form-control" rows="4">{{ old('descricao', $situacao->descricao) }}</textarea>
        </div>
        <div class="col-md-3">
          <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativo" {{ old('ativo', $situacao->ativo) ? 'checked' : '' }}>
            <label class="form-check-label" for="ativo">Ativo</label>
          </div>
        </div>
      </div>
      <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('situacoes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
      </div>
    </form>
  </div>
</div>
@endsection