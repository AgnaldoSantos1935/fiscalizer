@extends('layouts.app')
@section('title', 'Nova Empresa')

@section('content')
<h2 class="mb-4">Cadastrar Nova Empresa</h2>

<form action="{{ route('empresas.store') }}" method="POST" class="card p-4 shadow-sm">
  @csrf
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Razão Social *</label>
      <input type="text" name="razao_social" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">CNPJ *</label>
      <input type="text" name="cnpj" class="form-control" maxlength="14" required>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control">
    </div>
    <div class="col-md-6">
      <label class="form-label">Telefone</label>
      <input type="text" name="telefone" class="form-control">
    </div>
  </div>
  <div class="text-end">
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-success">Salvar</button>
  </div>
</form>
@endsection
