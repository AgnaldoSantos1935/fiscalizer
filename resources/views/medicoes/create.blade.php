@extends('layouts.app')
@section('title', 'Nova Medição')

@section('content')
<h2 class="mb-4">Cadastrar Nova Medição</h2>

<form action="{{ route('medicoes.store') }}" method="POST" class="card p-4 shadow-sm">
  @csrf
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Contrato *</label>
      <select name="contrato_id" class="form-select" required>
        <option value="">Selecione...</option>
        @foreach($contratos as $c)
          <option value="{{ $c->id }}">{{ $c->numero }} - {{ Str::limit($c->objeto, 50) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Mês Referência (AAAA-MM) *</label>
      <input type="text" name="mes_referencia" class="form-control" placeholder="2025-10" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Valor Unitário PF (R$) *</label>
      <input type="number" step="0.01" name="valor_unitario_pf" class="form-control" required>
    </div>
  </div>

  <div class="text-end">
    <a href="{{ route('medicoes.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-success">Salvar</button>
  </div>
</form>
@endsection
