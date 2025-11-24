@extends('layouts.app')
@section('title','Situação de Contrato')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Detalhes da Situação</h5>
      <div>
        <a href="{{ route('situacoes.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
        <a href="{{ route('situacoes.edit', $situacao->id) }}" class="btn btn-primary btn-sm">Editar</a>
      </div>
    </div>
    <div class="card-body">
      <div class="mb-2"><strong>Nome:</strong> {{ $situacao->nome }}</div>
      <div class="mb-2"><strong>Descrição:</strong> {{ $situacao->descricao ?? '—' }}</div>
      <div class="mb-2"><strong>Cor:</strong> {{ $situacao->cor ?? '—' }}</div>
      <div class="mb-2"><strong>Status:</strong> {!! $situacao->ativo ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-secondary">Inativo</span>' !!}</div>
    </div>
  </div>
</div>
@endsection