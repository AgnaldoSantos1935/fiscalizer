@extends('layouts.app')
@section('title','Cadastrar Ocorrência de Fiscalização')

@section('content_body')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-primary"></i>Cadastrar Ocorrência de Fiscalização</h5>
    </div>
    <form method="POST" action="{{ route('ocorrencias.store') }}">
      @csrf
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Contrato</label>
          <select name="contrato_id" class="form-select" required>
            <option value="">Selecione...</option>
            @foreach ($contratos as $c)
              <option value="{{ $c->id }}">{{ $c->numero }} — {{ $c->objeto }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tipo</label>
          <input type="text" name="tipo" class="form-control" required>
        </div>
        <div class="col-md-12">
          <label class="form-label">Descrição</label>
          <textarea name="descricao" class="form-control" rows="5" required></textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Data da Ocorrência</label>
          <input type="date" name="data_ocorrencia" class="form-control">
        </div>
      </div>
      <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('ocorrencias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Cadastrar Ocorrência</button>
      </div>
    </form>
  </div>
</div>
@endsection
