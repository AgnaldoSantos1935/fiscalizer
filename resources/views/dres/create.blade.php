@extends('layouts.app')

@section('title', 'Cadastrar DRE')

@section('content')
@include('layouts.components.breadcrumbs')
@section('breadcrumb')
  @include('layouts.components.breadcrumbs', [
    'trail' => [
      ['label' => 'DREs', 'icon' => 'fas fa-university', 'url' => route('dres.index')],
      ['label' => 'Nova DRE']
    ]
  ])
@endsection
<div class="container-fluid">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between rounded-top-4">
            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nova DRE</h4>
            <a href="{{ route('dres.index') }}" class="btn btn-light btn-sm text-primary">
                <i class="fas fa-arrow-left me-1"></i>Voltar
            </a>
        </div>

        <div class="card-body bg-light p-4">
            <form action="{{ route('dres.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Código *</label>
                        <input type="text" name="codigodre" class="form-control" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Nome *</label>
                        <input type="text" name="nome_dre" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Município Sede *</label>
                        <input type="text" name="municipio_sede" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Telefone</label>
                        <input type="text" name="telefone" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">CEP</label>
                        <input type="text" name="cep" class="form-control cep-input">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Logradouro</label>
                        <input type="text" name="logradouro" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Número</label>
                        <input type="text" name="numero" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Complemento</label>
                        <input type="text" name="complemento" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bairro</label>
                        <input type="text" name="bairro" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Latitude</label>
                        <input type="text" name="latitude" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Longitude</label>
                        <input type="text" name="longitude" class="form-control">
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('dres.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Cancelar</a>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
