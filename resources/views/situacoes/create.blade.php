@extends('layouts.app')
@section('title','Nova Situação de Contrato')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Cadastrar Situação</h5>
    </div>
    <form method="POST" action="{{ route('situacoes.store') }}">
      @csrf
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome</label>
          <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Cor</label>
          <input type="text" name="cor" class="form-control" placeholder="Ex.: bg-success">
        </div>
        <div class="col-md-12">
          <label class="form-label">Descrição</label>
          <textarea name="descricao" class="form-control" rows="4"></textarea>
        </div>
        <div class="col-md-3">
          <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativo">
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