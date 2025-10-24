@extends('layouts.app')
@section('title', 'Nova Função')

@section('content')
<h2>Nova Função para Medição {{ $medicao->mes_referencia }}</h2>

<form action="{{ route('funcoes.store') }}" method="POST" class="card p-4 shadow-sm">
  @csrf
  <input type="hidden" name="medicao_id" value="{{ $medicao->id }}">

  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Nome da Função *</label>
      <input type="text" name="nome_funcao" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Tipo *</label>
      <select name="tipo" class="form-select" required>
        <option value="">Selecione...</option>
        <option value="EE">EE - Entrada Externa</option>
        <option value="SE">SE - Saída Externa</option>
        <option value="CE">CE - Consulta Externa</option>
        <option value="ALI">ALI - Arquivo Lógico Interno</option>
        <option value="AIE">AIE - Arquivo de Interface Externa</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Complexidade *</label>
      <select name="complexidade" class="form-select" required>
        <option value="">Selecione...</option>
        <option value="baixa">Baixa</option>
        <option value="media">Média</option>
        <option value="alta">Alta</option>
      </select>
    </div>
  </div>

  <div class="text-end">
    <a href="{{ route('medicoes.show', $medicao->id) }}" class="btn btn-secondary">Voltar</a>
    <button type="submit" class="btn btn-success">Salvar</button>
  </div>
</form>
@endsection
