@extends('layouts.app')

@section('title', 'Boletim de Medição')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

  <!-- 🔹 Cabeçalho -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
      <h4 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Boletim de Medição Nº {{ $boletim->id }}</h4>
      <a href="{{ route('boletins.pdf', $boletim->id) }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">
<i class="fas fa-file-pdf text-danger me-1"></i> Download PDF
      </a>
    </div>
    <div class="card-body bg-white">
      <div class="row g-3">
        <div class="col-md-4">
          <p><strong>Projeto:</strong> {{ $boletim->projeto->nome ?? '—' }}</p>
          <p><strong>Medição:</strong> {{ $boletim->medicao->mes_referencia ?? '—' }}</p>
        </div>
        <div class="col-md-4">
          <p><strong>Contrato:</strong> {{ $boletim->medicao->contrato->numero ?? '—' }}</p>
          <p><strong>Data de Emissão:</strong> {{ $boletim->data_emissao->format('d/m/Y') }}</p>
        </div>
        <div class="col-md-4">
          <p><strong>Gerado por:</strong> Sistema Fiscalizer</p>
          <p><strong>Última Atualização:</strong> {{ $boletim->updated_at->format('d/m/Y H:i') }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- 🔹 Cards de resumo -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 bg-primary text-white rounded-4">
        <div class="card-body text-center">
          <i class="fas fa-cogs fa-2x mb-2"></i>
          <h5>{{ number_format($boletim->total_pf, 2, ',', '.') }}</h5>
          <p class="mb-0">Total de Pontos de Função</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 bg-success text-white rounded-4">
        <div class="card-body text-center">
          <i class="fas fa-microchip fa-2x mb-2"></i>
          <h5>{{ number_format($boletim->total_ust, 2, ',', '.') }}</h5>
          <p class="mb-0">Total de UST</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 bg-warning text-dark rounded-4">
        <div class="card-body text-center">
          <i class="fas fa-dollar-sign fa-2x mb-2"></i>
          <h5>R$ {{ number_format($boletim->valor_total, 2, ',', '.') }}</h5>
          <p class="mb-0">Valor Total</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 bg-info text-white rounded-4">
        <div class="card-body text-center">
          <i class="fas fa-chart-line fa-2x mb-2"></i>
          <h5>{{ $boletim->medicao->itens->where('projeto_id', $boletim->projeto_id)->count() }}</h5>
          <p class="mb-0">Itens de Medição</p>
        </div>
      </div>
    </div>
  </div>

  <!-- 🔹 Tabela de Itens -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-light d-flex align-items-center">
      <h5 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-list text-primary me-2"></i>Itens de Medição
      </h5>
    </div>
    <div class="card-body bg-white p-0">
      <table class="table table-hover table-striped mb-0">
        <thead class="table-light">
          <tr>
            <th>Descrição</th>
            <th class="text-end">Pontos de Função</th>
            <th class="text-end">UST</th>
            <th class="text-end">Valor (R$)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($boletim->medicao->itens->where('projeto_id', $boletim->projeto_id) as $item)
            <tr>
              <td>{{ $item->descricao ?? '—' }}</td>
              <td class="text-end">{{ number_format($item->pontos_funcao, 2, ',', '.') }}</td>
              <td class="text-end">{{ number_format($item->ust, 2, ',', '.') }}</td>
              <td class="text-end">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-3">Nenhum item de medição encontrado.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- 🔹 Gráficos -->
  <div class="row g-4 mb-5">
    <div class="col-md-8">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light">
          <h6 class="mb-0 text-secondary fw-semibold"><i class="fas fa-chart-bar text-primary me-1"></i>Pontos de Função e UST</h6>
        </div>
        <div class="card-body">
          <canvas id="graficoBarra"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light">
          <h6 class="mb-0 text-secondary fw-semibold"><i class="fas fa-chart-pie text-success me-1"></i>Distribuição de PF</h6>
        </div>
        <div class="card-body">
          <canvas id="graficoPizza"></canvas>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const labels = @json($boletim->medicao->itens->where('projeto_id', $boletim->projeto_id)->pluck('descricao'));
    const pfData = @json($boletim->medicao->itens->where('projeto_id', $boletim->projeto_id)->pluck('pontos_funcao'));
    const ustData = @json($boletim->medicao->itens->where('projeto_id', $boletim->projeto_id)->pluck('ust'));

    // 🔹 Gráfico de Barras
    new Chart(document.getElementById('graficoBarra'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pontos de Função (PF)',
                    backgroundColor: '#007bff',
                    data: pfData
                },
                {
                    label: 'UST',
                    backgroundColor: '#28a745',
                    data: ustData
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                x: { ticks: { autoSkip: false, maxRotation: 45, minRotation: 45 } },
                y: { beginAtZero: true }
            }
        }
    });

    // 🔹 Gráfico de Pizza
    new Chart(document.getElementById('graficoPizza'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Distribuição PF',
                data: pfData,
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                    '#6f42c1', '#20c997', '#fd7e14', '#343a40', '#adb5bd'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
});
</script>
@endsection
