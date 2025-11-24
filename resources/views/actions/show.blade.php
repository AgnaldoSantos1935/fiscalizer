@extends('layouts.app')

@section('title', 'Detalhes da Action')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Action</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('actions.edit', $action) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="{{ route('actions.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-outline card-secondary">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9 text-monospace">{{ $action->codigo }}</dd>

                <dt class="col-sm-3">Nome</dt>
                <dd class="col-sm-9">{{ $action->nome }}</dd>

                <dt class="col-sm-3">Descrição</dt>
                <dd class="col-sm-9">{{ $action->descricao ?? '—' }}</dd>

                <dt class="col-sm-3">Módulo</dt>
                <dd class="col-sm-9">{{ $action->modulo ?? '—' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection