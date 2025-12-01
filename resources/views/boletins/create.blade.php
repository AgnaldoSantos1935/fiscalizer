@extends('layouts.app')
@section('title','Cadastrar Boletim de Medição')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-file-signature me-2 text-primary"></i>Cadastrar Boletim</h5>
    </div>
    <form method="POST" action="{{ route('boletins.store') }}">
      @csrf
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Medição</label>
          <select name="medicao_id" class="form-select" required>
            <option value="">Selecione...</option>
            @foreach ($medicoes as $m)
              <option value="{{ $m->id }}">{{ $m->contrato->numero ?? 'Contrato' }} — {{ $m->competencia }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Projeto</label>
          <select name="projeto_id" class="form-select" required>
            <option value="">Selecione...</option>
            @foreach ($projetos as $p)
              <option value="{{ $p->id }}">{{ $p->nome ?? ('Projeto ' . $p->id) }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('boletins.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Gerar</button>
      </div>
    </form>
  </div>
</div>
@endsection
