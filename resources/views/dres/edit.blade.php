@extends('layouts.app')
@section('title','Editar DRE')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar DRE</h5>
    </div>
    <form action="{{ route('dres.update', $dre->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="card-body row g-3">
        <div class="col-md-3">
          <label class="form-label">Código</label>
          <input type="text" name="codigodre" class="form-control" value="{{ old('codigodre', $dre->codigodre) }}" required>
        </div>
        <div class="col-md-9">
          <label class="form-label">Nome</label>
          <input type="text" name="nome_dre" class="form-control" value="{{ old('nome_dre', $dre->nome_dre) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Município Sede</label>
          <input type="text" name="municipio_sede" class="form-control" value="{{ old('municipio_sede', $dre->municipio_sede) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email', $dre->email) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Telefone</label>
          <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $dre->telefone) }}">
        </div>
        <div class="col-md-12">
          <label class="form-label">Endereço</label>
          <input type="text" name="endereco" class="form-control" value="{{ old('endereco', $dre->endereco) }}">
        </div>
      </div>
      <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('dres.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
      </div>
    </form>
  </div>
</div>
@endsection