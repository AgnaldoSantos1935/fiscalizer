@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
            <li class="breadcrumb-item">
                <a href="{{ route('empenhos.index') }}" class="text-decoration-none text-primary fw-semibold">
                    <i class="fas fa-file-invoice-dollar me-1"></i>Empenhos
                </a>
            </li>
            @if(isset($contrato))
                <li class="breadcrumb-item">
                    <a href="{{ route('contratos.show', $contrato->id) }}" class="text-decoration-none text-primary fw-semibold">
                        Contrato nº {{ $contrato->numero }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-secondary fw-semibold" aria-current="page">Novo Empenho</li>
            @else
                <li class="breadcrumb-item active text-secondary fw-semibold" aria-current="page">Novo Empenho</li>
            @endif
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Novo Empenho</div>

                <div class="card-body">

                    @if(isset($contrato))
                        <!-- Compact breadcrumb-styled contract info -->
                        <nav aria-label="Contrato" class="mb-3">
                            <div class="p-2 bg-light rounded d-flex flex-wrap align-items-center gap-2">
                                <a href="{{ route('contratos.show', $contrato->id) }}" class="text-decoration-none">
                                    <span class="badge bg-primary">Contrato nº {{ $contrato->numero }}</span>
                                </a>
                                <span class="text-muted small">•</span>
                                <span class="badge bg-secondary text-white">{{ $contrato->contratada->razao_social ?? '-' }}</span>
                            </div>
                        </nav>
                    @endif

                    <form method="POST" action="{{ route('empenhos.store') }}">
                        @csrf

                        {{-- Contrato vinculado (preenchido pela página que chamou) --}}
                        <input type="hidden" name="contrato_id" value="{{ request('contrato_id') ?? old('contrato_id') }}">

                        <div class="form-group row mb-3">
                            <label for="numero" class="col-md-4 col-form-label text-md-right">Número do Empenho</label>
                            <div class="col-md-6">
                                <input id="numero" type="text" class="form-control @error('numero') is-invalid @enderror" name="numero" value="{{ old('numero') }}" required>
                                @error('numero')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="data_empenho" class="col-md-4 col-form-label text-md-right">Data do Empenho</label>
                            <div class="col-md-6">
                                <input id="data_empenho" type="date" class="form-control @error('data_empenho') is-invalid @enderror" name="data_empenho" value="{{ old('data_empenho') }}" required>
                                @error('data_empenho')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="valor" class="col-md-4 col-form-label text-md-right">Valor</label>
                            <div class="col-md-6">
                                <input id="valor" type="number" step="0.01" class="form-control @error('valor') is-invalid @enderror" name="valor" value="{{ old('valor') }}" required>
                                @error('valor')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="descricao" class="col-md-4 col-form-label text-md-right">Descrição</label>
                            <div class="col-md-6">
                                <textarea id="descricao" class="form-control @error('descricao') is-invalid @enderror" name="descricao">{{ old('descricao') }}</textarea>
                                @error('descricao')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Cadastrar Empenho
                                </button>
                                @if(isset($contrato))
                                    <a href="{{ route('contratos.show', $contrato->id) }}" class="btn btn-secondary">Voltar</a>
                                @else
                                    <a href="{{ route('empenhos.index') }}" class="btn btn-secondary">Voltar</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
