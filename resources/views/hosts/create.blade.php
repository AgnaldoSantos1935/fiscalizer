@extends('layouts.app')

@section('title', 'Novo Host')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h4 class="text-secondary fw-semibold mb-0">
        <i class="fas fa-server text-primary me-2"></i> Cadastrar Novo Host
      </h4>
      <a href="{{ route('hosts.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Voltar
      </a>
    </div>

    <div class="card-body">
      <form action="{{ route('hosts.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        {{-- Linha 1: Nome e Endereço --}}
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nome" class="form-label fw-semibold text-secondary">Nome *</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome') }}"
              class="form-control @error('nome') is-invalid @enderror"
              placeholder="Ex: Servidor GLPI, Portal SEDUC" required>
            @error('nome')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="endereco" class="form-label fw-semibold text-secondary">Endereço (IP ou Domínio) *</label>
            <input type="text" name="endereco" id="endereco" value="{{ old('endereco') }}"
              class="form-control @error('endereco') is-invalid @enderror"
              placeholder="Ex: 10.1.1.1 ou www.google.com" required>
            @error('endereco')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Linha 2: Tipo e Porta --}}
        <div class="row mb-3">
          <div class="col-md-4">
            <label for="tipo" class="form-label fw-semibold text-secondary">Tipo *</label>
            <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
              <option value="link" {{ old('tipo') === 'link' ? 'selected' : '' }}>Link (HTTP/HTTPS)</option>
              <option value="ip" {{ old('tipo') === 'ip' ? 'selected' : '' }}>Endereço IP</option>
            </select>
            @error('tipo')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-4">
            <label for="porta" class="form-label fw-semibold text-secondary">Porta (opcional)</label>
            <input type="number" name="porta" id="porta" value="{{ old('porta') }}"
              class="form-control @error('porta') is-invalid @enderror" placeholder="Ex: 80, 443">
            @error('porta')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-4">
            <label for="localizacao" class="form-label fw-semibold text-secondary">Localização</label>
            <input type="text" name="localizacao" id="localizacao" value="{{ old('localizacao') }}"
              class="form-control @error('localizacao') is-invalid @enderror" placeholder="Ex: DRE Marabá, Sede SEDUC">
            @error('localizacao')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Linha 3: Descrição --}}
        <div class="mb-3">
          <label for="descricao" class="form-label fw-semibold text-secondary">Descrição / Observações</label>
          <textarea name="descricao" id="descricao" rows="3"
            class="form-control @error('descricao') is-invalid @enderror"
            placeholder="Ex: Servidor responsável pelo sistema GLPI da DRE.">{{ old('descricao') }}</textarea>
          @error('descricao')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Linha 4: Ativo --}}
        <div class="form-check form-switch mb-4">
          <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1" {{ old('ativo', true) ? 'checked' : '' }}>
          <label class="form-check-label fw-semibold text-secondary" for="ativo">Ativo (incluir nos testes automatizados)</label>
        </div>

        {{-- Botões --}}
        <div class="text-end">
          <a href="{{ route('hosts.index') }}" class="btn btn-outline-secondary">
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

{{-- Validação visual Bootstrap --}}
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
