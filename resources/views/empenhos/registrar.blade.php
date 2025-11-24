@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h3>Registrar Empenho</h3>
      <p class="text-muted">Contrato: <strong>{{ $sol->contrato->numero ?? '—' }}</strong> · Solicitação #{{ $sol->id }}</p>

      <form method="POST" action="{{ route('financeiro.solicitacoes.registrar_empenho.store', $sol->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Número do Empenho</label>
                <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror" required value="{{ old('numero') }}">
                @error('numero')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">Data do Empenho</label>
                <input type="date" name="data_empenho" class="form-control @error('data_empenho') is-invalid @enderror" required value="{{ old('data_empenho') }}">
                @error('data_empenho')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">Valor</label>
                <input type="number" step="0.01" name="valor" class="form-control @error('valor') is-invalid @enderror" required value="{{ old('valor') }}">
                @error('valor')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Empresa (opcional)</label>
                <select name="empresa_id" class="form-select @error('empresa_id') is-invalid @enderror">
                  <option value="">— Selecione —</option>
                  @foreach($empresas as $empresa)
                    <option value="{{ $empresa->id }}" @selected(old('empresa_id')==$empresa->id)>{{ $empresa->razao_social }}</option>
                  @endforeach
                </select>
                @error('empresa_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Número do Processo (opcional)</label>
                <input type="text" name="processo" class="form-control @error('processo') is-invalid @enderror" value="{{ old('processo', $sol->numero_processo) }}">
                @error('processo')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-12">
                <label class="form-label">PDF Oficial do Empenho (opcional)</label>
                <input type="file" name="pdf_oficial" class="form-control @error('pdf_oficial') is-invalid @enderror" accept="application/pdf">
                @error('pdf_oficial')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="btn btn-primary">Registrar Empenho</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection