@extends('layouts.app')

@section('title', 'Detalhes da Nota de Empenho')

@section('content')
<div class="container-fluid">
  <div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
      <h4 class="mb-0 fw-semibold">
        <i class="fas fa-file-invoice-dollar me-2"></i> Detalhes da Nota de Empenho
      </h4>
      <a href="{{ route('empenhos.index') }}" class="btn btn-light btn-sm text-primary fw-semibold">
        <i class="fas fa-arrow-left"></i> Voltar
      </a>
    </div>

    <div class="card-body bg-light">

      {{-- 🔹 Informações principais --}}
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white">
          <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-info-circle me-2"></i> Dados da Nota</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">Número da NE</label>
              <p class="form-control-plaintext">{{ $empenho->numero }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">Data de Lançamento</label>
              <p class="form-control-plaintext">{{ $empenho->data_lancamento?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">Processo</label>
              <p class="form-control-plaintext">{{ $empenho->processo ?? '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      {{-- 🔹 Contrato e empresa --}}
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white">
          <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-link me-2"></i> Contrato e Empresa</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary">Contrato</label>
              <p class="form-control-plaintext">
                {{ $empenho->contrato?->numero ?? $empenho->contrato_numero ?? '—' }}
              </p>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary">Empresa</label>
              <p class="form-control-plaintext">{{ $empenho->empresa?->razao_social ?? $empenho->credor_nome ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">CNPJ</label>
              <p class="form-control-plaintext">{{ $empenho->cnpj ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">Valor Global (R$)</label>
              <p class="form-control-plaintext">R$ {{ number_format($empenho->valor_total, 2, ',', '.') }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">Valor por Extenso</label>
              <p class="form-control-plaintext">{{ $empenho->valor_extenso ?? '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      {{-- 🔹 Dados orçamentários --}}
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white">
          <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-coins me-2"></i> Dados Orçamentários</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">Programa de Trabalho</label>
              <p class="form-control-plaintext">{{ $empenho->programa_trabalho ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">Fonte de Recurso</label>
              <p class="form-control-plaintext">{{ $empenho->fonte_recurso ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">Natureza da Despesa</label>
              <p class="form-control-plaintext">{{ $empenho->natureza_despesa ?? '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      {{-- 🔹 Ordenador --}}
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white">
          <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-user-tie me-2"></i> Ordenador de Despesa</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold text-secondary">Nome</label>
              <p class="form-control-plaintext">{{ $empenho->ordenador_nome ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary">CPF</label>
              <p class="form-control-plaintext">{{ $empenho->ordenador_cpf ?? '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      {{-- 🔹 Itens --}}
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white">
          <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-list me-2"></i> Itens da Nota</h5>
        </div>
        <div class="card-body">
          @if($empenho->itens->count())
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th>Descrição</th>
                  <th>Unidade</th>
                  <th>Quantidade</th>
                  <th>Valor Unitário (R$)</th>
                  <th>Valor Total (R$)</th>
                </tr>
              </thead>
              <tbody>
                @foreach($empenho->itens as $item)
                  <tr>
                    <td>{{ $item->descricao }}</td>
                    <td>{{ $item->unidade ?? '—' }}</td>
                    <td class="text-end">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($item->valor_total, 2, ',', '.') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="text-end mt-3">
            <h5 class="fw-semibold text-secondary">
              Total Geral:
              <span class="fw-bold text-dark">R$ {{ number_format($empenho->itens->sum('valor_total'), 2, ',', '.') }}</span>
            </h5>
          </div>
          @else
            <div class="alert alert-light border text-center text-muted">
              Nenhum item cadastrado nesta Nota de Empenho.
            </div>
          @endif
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
