@extends('layouts.app')
@section('title', 'Novo Contrato')

@section('content')
<h2 class="mb-4">Cadastrar Novo Contrato</h2>

<form action="{{ route('contratos.store') }}" method="POST" class="card p-4 shadow-sm">
  @csrf
  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Número *</label>
      <input type="text" name="numero" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Valor Global (R$) *</label>
      <input type="number" step="0.01" name="valor_global" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Empresa Contratada *</label>
      <select name="contratada_id" class="form-select" required>
        <option value="">Selecione...</option>
        @foreach($empresas as $empresa)
          <option value="{{ $empresa->id }}">{{ $empresa->razao_social }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Objeto *</label>
    <textarea name="objeto" rows="3" class="form-control" required></textarea>
  </div>
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Data Início</label>
      <input type="date" name="data_inicio" class="form-control">
    </div>
    <div class="col-md-6">
      <label class="form-label">Data Fim</label>
      <input type="date" name="data_fim" class="form-control">
    </div>
  </div>
  <div class="text-end">
    <a href="{{ route('contratos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button id="btnsalvar" type="submit" class="btn btn-success">Salvar</button>
  </div>
</form>
@endsection
@section('css')

@endsection
@section('js')
<script>

</script>

@endsection
