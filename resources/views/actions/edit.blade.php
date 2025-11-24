@extends('layouts.app')

@section('title', 'Editar Action')

@section('content')
<div class="container">
    <h3 class="mb-4">Editar Action</h3>

    <form action="{{ route('actions.update', $action) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $action->codigo) }}" class="form-control @error('codigo') is-invalid @enderror">
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome', $action->nome) }}" class="form-control @error('nome') is-invalid @enderror">
            @error('nome')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3" class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $action->descricao) }}</textarea>
            @error('descricao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="modulo" class="form-label">Módulo</label>
            <input type="text" name="modulo" id="modulo" value="{{ old('modulo', $action->modulo) }}" class="form-control @error('modulo') is-invalid @enderror">
            @error('modulo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('actions.show', $action) }}" class="btn btn-outline-secondary">Voltar</a>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection