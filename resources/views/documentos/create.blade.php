@extends('layouts.app')
@section('title','Novo Documento')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-file-upload me-2 text-primary"></i>Cadastrar Documento</h5>
    </div>
    <form method="POST" action="{{ route('documentos.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="card-body row g-3">
        <div class="col-md-4">
          <label class="form-label">Contrato</label>
          <select name="contrato_id" class="form-select" required>
            <option value="">Selecione...</option>
            @foreach ($contratos as $c)
              <option value="{{ $c->id }}">{{ $c->numero }} — {{ $c->objeto }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tipo</label>
          <input type="text" name="tipo" class="form-control" placeholder="Ex.: PDF, Relatório" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Título</label>
          <input type="text" name="titulo" class="form-control" placeholder="Opcional">
        </div>
        <div class="col-md-12">
          <label class="form-label">Arquivo</label>
          <input type="file" name="caminho_arquivo" class="form-control" required>
        </div>
      </div>
      <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('documentos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
      </div>
    </form>
  </div>
  </div>
@endsection