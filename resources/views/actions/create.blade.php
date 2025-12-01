@extends('layouts.app')

@section('title', 'Cadastrar Action')

@section('content_body')
<div class="container">
    <h3 class="mb-4">Cadastrar Action</h3>

    <form action="{{ route('actions.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}" class="form-control @error('codigo') is-invalid @enderror" placeholder="ex: contratos_view">
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome') }}" class="form-control @error('nome') is-invalid @enderror" placeholder="ex: Visualizar Contratos">
            @error('nome')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3" class="form-control @error('descricao') is-invalid @enderror" placeholder="Opcional">{{ old('descricao') }}</textarea>
            @error('descricao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="modulo" class="form-label">Módulo</label>
            <input type="text" name="modulo" id="modulo" value="{{ old('modulo') }}" class="form-control @error('modulo') is-invalid @enderror" placeholder="ex: contratos">
            @error('modulo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('actions.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Cadastrar Action</button>
        </div>
    </form>
</div>
@endsection
