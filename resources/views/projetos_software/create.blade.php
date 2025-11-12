@extends('layouts.app')
@section('title', 'Cadastrar Projeto')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0"><i class="fas fa-folder-plus me-2"></i>Novo Projeto</h4>
    </div>

    <form action="{{ route('projetos.store') }}" method="POST" class="p-4">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nome do Projeto</label>
          <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Contrato Vinculado</label>
          <select name="contrato_id" class="form-select">
            @foreach($contratos as $c)
              <option value="{{ $c->id }}">{{ $c->numero }} — {{ $c->objeto }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Data Início</label>
          <input type="date" name="data_inicio" class="form-control">
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Data Fim</label>
          <input type="date" name="data_fim" class="form-control">
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            <option value="Planejado">Planejado</option>
            <option value="Em execução">Em execução</option>
            <option value="Concluído">Concluído</option>
            <option value="Paralisado">Paralisado</option>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold">Descrição / Escopo</label>
          <textarea name="descricao" class="form-control" rows="4"></textarea>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-success px-4"><i class="fas fa-save me-1"></i>Salvar Projeto</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
