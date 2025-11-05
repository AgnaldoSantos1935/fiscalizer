
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Nota de Empenho {{ $nota->numero }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; color: #333; }
    .header { text-align: center; border-bottom: 2px solid #555; margin-bottom: 10px; }
    .header img { width: 80px; position: absolute; left: 30px; top: 10px; }
    .header h2 { margin: 0; font-size: 16px; }
    .header h3 { margin: 2px 0 0 0; font-size: 14px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #888; padding: 6px; text-align: left; }
    th { background-color: #f2f2f2; text-align: center; }
    .text-end { text-align: right; }
    .center { text-align: center; }
    .footer { text-align: center; font-size: 10px; color: #777; margin-top: 40px; }
  </style>
</head>
<body>

  <div class="header">
    <img src="{{ public_path('images/logo_seduc.png') }}" alt="Logo SEDUC">
    <h2>GOVERNO DO ESTADO DO PARÁ</h2>
    <h3>SECRETARIA DE ESTADO DE EDUCAÇÃO - SEDUC</h3>
    <h3>NOTA DE EMPENHO Nº {{ $nota->numero }}</h3>
  </div>

  <table>
    <tr>
      <th style="width: 30%">Data de Lançamento</th>
      <td>{{ $nota->data_lancamento?->format('d/m/Y') ?? '—' }}</td>
      <th style="width: 30%">Processo</th>
      <td>{{ $nota->processo ?? '—' }}</td>
    </tr>
    <tr>
      <th>Contrato</th>
      <td>{{ $nota->contrato?->numero ?? '—' }}</td>
      <th>Natureza da Despesa</th>
      <td>{{ $nota->natureza_despesa ?? '—' }}</td>
    </tr>
    <tr>
      <th>Credor</th>
      <td colspan="3">{{ $nota->empresa?->razao_social ?? $nota->credor_nome }} ({{ $nota->cnpj }})</td>
    </tr>
    <tr>
      <th>Programa de Trabalho (PTRES)</th>
      <td>{{ $nota->programa_trabalho ?? '—' }}</td>
      <th>Fonte de Recurso</th>
      <td>{{ $nota->fonte_recurso ?? '—' }}</td>
    </tr>
  </table>

  <h4 style="margin-top: 20px;">Itens da Nota de Empenho</h4>
  <table>
    <thead>
      <tr>
        <th style="width: 5%">Item</th>
        <th>Descrição</th>
        <th style="width: 10%">Unid.</th>
        <th style="width: 10%">Qtd</th>
        <th style="width: 15%">Vlr Unit. (R$)</th>
        <th style="width: 15%">Vlr Total (R$)</th>
      </tr>
    </thead>
    <tbody>
      @forelse($nota->itens as $item)
      <tr>
        <td class="center">{{ $loop->iteration }}</td>
        <td>{{ $item->descricao }}</td>
        <td class="center">{{ $item->unidade ?? '—' }}</td>
        <td class="center">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
        <td class="text-end">{{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
        <td class="text-end fw-bold">{{ number_format($item->valor_total, 2, ',', '.') }}</td>
      </tr>
      @empty
      <tr><td colspan="6" class="center text-muted">Nenhum item registrado.</td></tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <th colspan="5" class="text-end">TOTAL GERAL</th>
        <th class="text-end fw-bold">R$ {{ number_format($nota->itens->sum('valor_total'), 2, ',', '.') }}</th>
      </tr>
    </tfoot>
  </table>

  <p style="margin-top: 20px;"><strong>Valor por extenso:</strong> {{ ucfirst($nota->valor_extenso ?? '—') }}</p>

  <div style="margin-top: 60px; text-align: center;">
    <p class="fw-semibold">{{ $nota->ordenador_nome ?? '____________________________' }}</p>
    <small>Ordenador de Despesa</small><br>
    @if($nota->ordenador_cpf)
      <small>CPF: {{ $nota->ordenador_cpf }}</small>
    @endif
  </div>

  <div class="footer">
    <p>Gerado automaticamente pelo Sistema Fiscalizer - SEDUC/PA<br>
    {{ now()->format('d/m/Y H:i') }}</p>
  </div>

</body>
</html>
