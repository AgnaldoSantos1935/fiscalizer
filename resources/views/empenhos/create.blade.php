@extends('layouts.app')
@section('title', 'Nova Nota de Empenho')

@section('content')
<div class="container-fluid">

  {{-- 🔹 Notificações --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3">
      <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- 🔷 CARD PRINCIPAL --}}
  <div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
      <h4 class="mb-0 fw-semibold">
        <i class="fas fa-file-invoice-dollar me-2"></i> Cadastrar Nota de Empenho
      </h4>
      <a href="{{ route('empenhos.index') }}" class="btn btn-light btn-sm text-primary fw-semibold">
        <i class="fas fa-arrow-left"></i> Voltar
      </a>
    </div>

    <div class="card-body bg-light">
      <form action="{{ route('empenhos.store') }}" method="POST" id="formNotaEmpenho">
        @csrf

        {{-- 🔹 DADOS PRINCIPAIS --}}
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white">
            <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-info-circle me-2"></i> Dados Principais</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary">Número da NE *</label>
                <input type="text" name="numero" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary">Data de Lançamento *</label>
                <input type="date" name="data_lancamento" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary">Processo *</label>
                <input type="text" name="processo" class="form-control form-control-sm" placeholder="Ex: 2025/1156453" required>
              </div>
            </div>
          </div>
        </div>

        {{-- 🔹 CONTRATO / EMPRESA --}}
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white">
            <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-link me-2"></i> Contrato e Empresa</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary">Contrato *</label>
                <select name="contrato_id" class="form-select form-select-sm" required>
                  <option value="">Selecione...</option>
                  @foreach($contratos as $contrato)
                    <option value="{{ $contrato->id }}">{{ $contrato->numero }} - {{ \Illuminate\Support\Str::limit($contrato->objeto, 60) }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary">Empresa</label>
                <input type="text" name="credor_nome" class="form-control form-control-sm" readonly>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary">CNPJ</label>
                <input type="text" name="cnpj" class="form-control form-control-sm" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary">Número do Contrato</label>
                <input type="text" name="contrato_numero" class="form-control form-control-sm" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary">Valor Global (R$)</label>
                <input type="number" step="0.01" name="valor_total" class="form-control form-control-sm" readonly>
              </div>
            </div>
          </div>
        </div>

        {{-- 🔹 DADOS ORÇAMENTÁRIOS --}}
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white">
            <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-coins me-2"></i> Dados Orçamentários</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label text-secondary fw-semibold">Programa de Trabalho (PTRES)</label>
                <input type="text" name="programa_trabalho" class="form-control form-control-sm">
              </div>
              <div class="col-md-4">
                <label class="form-label text-secondary fw-semibold">Fonte de Recurso</label>
                <input type="text" name="fonte_recurso" class="form-control form-control-sm">
              </div>
              <div class="col-md-4">
                <label class="form-label text-secondary fw-semibold">Natureza da Despesa</label>
                <input type="text" name="natureza_despesa" class="form-control form-control-sm">
              </div>
            </div>
          </div>
        </div>

        {{-- 🔹 ORDENADOR --}}
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white">
            <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-user-tie me-2"></i> Ordenador de Despesa</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label text-secondary fw-semibold">Nome</label>
                <input type="text" name="ordenador_nome" class="form-control form-control-sm">
              </div>
              <div class="col-md-4">
                <label class="form-label text-secondary fw-semibold">CPF</label>
                <input type="text" name="ordenador_cpf" class="form-control form-control-sm" placeholder="000.000.000-00">
              </div>
            </div>
          </div>
        </div>

        {{-- 🔹 ITENS --}}
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white">
            <h5 class="text-primary fw-semibold mb-0"><i class="fas fa-list me-2"></i> Itens da Nota</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-sm table-bordered align-middle" id="tabelaItens">
                <thead class="table-light text-center">
                  <tr>
                    <th>Descrição</th>
                    <th>Unidade</th>
                    <th>Qtd</th>
                    <th>Vlr Unitário (R$)</th>
                    <th>Vlr Total (R$)</th>
                    <th style="width: 5%">🗑️</th>
                  </tr>
                </thead>
                <tbody id="itensBody"></tbody>
              </table>
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm" id="addItem">
              <i class="fas fa-plus"></i> Adicionar Item
            </button>

            <div class="mt-4 text-end">
              <h5 class="text-secondary mb-0">
                Valor Total: <span id="valorTotal" class="fw-bold text-dark">R$ 0,00</span>
              </h5>
            </div>
          </div>
        </div>

        {{-- 🔹 BOTÕES --}}
        <div class="card-footer bg-white border-0 text-end">
          <a href="{{ route('empenhos.index') }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-times"></i> Cancelar
          </a>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Salvar Nota
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- 🔹 JavaScript Corrigido --}}
<script>


document.addEventListener("DOMContentLoaded", () => {
  const contratoSelect = document.querySelector('select[name="contrato_id"]');

  contratoSelect.addEventListener('change', async (e) => {
    const contratoId = e.target.value;
    if (!contratoId) return;

    try {
      const response = await fetch(`/ajax/contratos/${contratoId}`); // ✅ CORRIGIDO: usa crases e rota ajax
      const text = await response.text();

      console.log("🔍 Retorno da API:", text.substring(0, 200));

      if (text.trim().startsWith('<')) {
        alert("Erro: rota retornou HTML (verifique autenticação ou rota).");
        return;
      }

      const data = JSON.parse(text);

      document.querySelector('input[name="contrato_numero"]').value = data.numero ?? '';
      document.querySelector('input[name="credor_nome"]').value = data.empresa?.razao_social ?? '';
      document.querySelector('input[name="cnpj"]').value = data.empresa?.cnpj ?? '';
      document.querySelector('input[name="valor_total"]').value = data.valor_global ?? 0;
    } catch (err) {
      console.error("⚠️ Erro ao obter contrato:", err);
      alert("Falha ao carregar dados do contrato.");
    }
  });
});
</script>
@endsection
