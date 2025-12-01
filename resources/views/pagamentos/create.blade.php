@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h3>Cadastrar Pagamento</h3>
      <p class="text-muted">Empenho: <strong>{{ $empenho->numero }}</strong> · Contrato: <strong>{{ $empenho->contrato->numero ?? '—' }}</strong></p>

      <form method="POST" action="{{ route('financeiro.pagamentos.store', $empenho->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Data do Pagamento</label>
                <input type="date" name="data_pagamento" class="form-control @error('data_pagamento') is-invalid @enderror" required value="{{ old('data_pagamento') }}">
                @error('data_pagamento')
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
              <div class="col-md-4">
                <label class="form-label">Observação (opcional)</label>
                <input type="text" name="observacao" class="form-control @error('observacao') is-invalid @enderror" value="{{ old('observacao') }}">
                @error('observacao')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-12">
                <label class="form-label">Comprovante (PDF opcional)</label>
                <input type="file" name="arquivo_comprovante_pdf" class="form-control @error('arquivo_comprovante_pdf') is-invalid @enderror" accept="application/pdf">
                @error('arquivo_comprovante_pdf')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="btn btn-success">Cadastrar Pagamento</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
