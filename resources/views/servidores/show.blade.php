@extends('layouts.app')
@section('title','Servidor')

@section('content')
<div class="container-fluid">
  <div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between">
      <h5 class="mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Dados do Servidor</h5>
      <div>
        <a href="{{ route('servidores.edit', $servidor) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
        <a href="{{ route('servidores.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
      </div>
    </div>
    <div class="card-body row g-3">
      <div class="col-md-6">
        <h6 class="text-secondary">Pessoa</h6>
        <p class="mb-1"><strong>Nome:</strong> {{ $servidor->pessoa->nome_completo }}</p>
        <p class="mb-1"><strong>CPF:</strong> {{ $servidor->pessoa->cpf }}</p>
        <p class="mb-1"><strong>Email:</strong> {{ $servidor->pessoa->email ?? '—' }}</p>
        @if($servidor->pessoa->user)
          <p class="mb-1"><strong>Usuário do sistema:</strong> {{ $servidor->pessoa->user->name }} (ID {{ $servidor->pessoa->user->id }})</p>
        @endif
      </div>
      <div class="col-md-6">
        <h6 class="text-secondary">Funcionais</h6>
        <p class="mb-1"><strong>Matrícula:</strong> {{ $servidor->matricula }}</p>
        <p class="mb-1"><strong>Cargo:</strong> {{ $servidor->cargo ?? '—' }}</p>
        <p class="mb-1"><strong>Função:</strong> {{ $servidor->funcao ?? '—' }}</p>
        <p class="mb-1"><strong>Lotação:</strong> {{ $servidor->lotacao ?? '—' }}</p>
        <p class="mb-1"><strong>Vínculo:</strong> {{ $servidor->vinculo ?? '—' }}</p>
        <p class="mb-1"><strong>Situação:</strong> {{ $servidor->situacao }}</p>
        <p class="mb-1"><strong>Admissão:</strong> {{ optional($servidor->data_admissao)->format('d/m/Y') ?? '—' }}</p>
        <p class="mb-1"><strong>Salário:</strong> {{ $servidor->salario ? 'R$ '.number_format($servidor->salario,2,',','.') : '—' }}</p>
      </div>
    </div>
  </div>
</div>
@endsection
