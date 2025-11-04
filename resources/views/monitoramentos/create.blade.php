@extends('layouts.app')

@section('title', 'Novo Monitoramento')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h4 class="text-secondary fw-semibold mb-0">
        <i class="fas fa-plus-circle text-primary me-2"></i>
        Novo Monitoramento de Rede
      </h4>
      <a href="{{ route('monitoramentos.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Voltar
      </a>
    </div>

    <div class="card-body">
      <form action="{{ route('monitoramentos.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nome" class="form-label fw-semibold text-secondary">Nome *</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome') }}" class="form-control @error('nome') is-invalid @enderror" required>
            @error('nome')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-3">
            <label for="tipo" class="form-label fw-semibold text-secondary">Tipo *</label>
            <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
              <option value="link" {{ old('tipo') === 'link' ? 'selected' : '' }}>Link (HTTP/HTTPS)</option>
              <option value="ip" {{ old('tipo') === 'ip' ? 'selected' : '' }}>IP</option>
            </select>
            @error('tipo')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-3">
            <label for="porta" class="form-label fw-semibold text-secondary">Porta</label>
            <input type="number" name="porta" id="porta" value="{{ old('porta') }}" class="form-control @error('porta') is-invalid @enderror" placeholder="Ex: 80, 443">
            @error('porta')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="mb-3">
          <label for="alvo" class="form-label fw-semibold text-secondary">Alvo *</label>
          <input type="text" name="alvo" id="alvo" value="{{ old('alvo') }}" class="form-control @error('alvo') is-invalid @enderror" placeholder="Ex: https://portal.seduc.pa.gov.br ou 10.1.1.1" required>
          @error('alvo')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-check form-switch mb-4">
          <input class="form-check-input" type="checkbox" id="ativo" name="ativo" {{ old('ativo', true) ? 'checked' : '' }}>
          <label class="form-check-label fw-semibold text-secondary" for="ativo">Ativo</label>
        </div>

        <div class="text-end">
          <a href="{{ route('monitoramentos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-times"></i> Cancelar
          </a>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Salvar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Validação front-end Bootstrap --}}
<script>
(function () {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>
@endsection
