<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Nota de Empenho Nº {{ $nota->numero }}</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 11pt;
      margin: 40px 50px;
      color: #222;
    }
    header {
      text-align: center;
      border-bottom: 2px solid #0a472e;
      margin-bottom: 20px;
      padding-bottom: 10px;
    }
    header img {
      height: 65px;
      margin-right: 10px;
      vertical-align: middle;
    }
    header h1 {
      font-size: 15pt;
      margin: 0;
      color: #0a472e;
      text-transform: uppercase;
    }
    header h2 {
      font-size: 11pt;
      margin: 0;
      color: #555;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 6px;
    }
    th {
      background-color: #f5f5f5;
      font-weight: bold;
      text-align: center;
    }
    .right { text-align: right; }
    .center { text-align: center; }
    .no-border { border: none !important; }
    footer {
      position: fixed;
      bottom: 30px;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 9pt;
      color: #777;
    }
  </style>
</head>
<body>
  <header>
    <img src="{{ public_path('images/logo_seduc_pa.png') }}" alt="Logo SEDUC">
    <h1>Secretaria de Estado de Educação - SEDUC/PA</h1>
    <h2>Sistema Fiscalizer • Nota de Empenho</h2>
  </header>

  <h3 style="text-align:center; text-decoration: underline; margin-top:0;">Nota de Empenho Nº {{ $nota->numero }}</h3>

  <table class="no-border" style="margin-bottom: 20px;">
    <tr>
      <td class="no-border"><strong>Empresa:</strong> {{ $nota->empresa->razao_social ?? '—' }}</td>
      <td class="no-border"><strong>CNPJ:</strong> {{ $nota->empresa->cnpj ?? '—' }}</td>
    </tr>
    <tr>
      <td class="no-border"><strong>Contrato:</strong> {{ $nota->contrato->numero ?? '—' }}</td>
      <td class="no-border"><strong>Processo:</strong> {{ $nota->processo ?? '—' }}</td>
    </tr>
    <tr>
      <td class="no-border"><strong>Programa de Trabalho:</strong> {{ $nota->programa_trabalho ?? '—' }}</td>
      <td class="no-border"><strong>Fonte de Recurso:</strong> {{ $nota->fonte_recurso ?? '—' }}</td>
    </tr>
    <tr>
      <td class="no-border"><strong>Natureza da Despesa:</strong> {{ $nota->natureza_despesa ?? '—' }}</td>
      <td class="no-border"><strong>Data:</strong> {{ $nota->data_formatada }}</td>
    </tr>
  </table>

  <h4 style="margin-top: 10px; color: #0a472e;">Itens do Empenho</h4>
  <table>
    <thead>
      <tr>
        <th style="width: 5%">#</th>
        <th>Descrição</th>
        <th style="width: 10%">Unidade</th>
        <th style="width: 10%">Qtd</th>
        <th style="width: 15%">Valor Unit.</th>
        <th style="width: 15%">Valor Total</th>
      </tr>
    </thead>
    <tbody>
      @forelse($nota->itens as $i => $item)
        <tr>
          <td class="center">{{ $i + 1 }}</td>
          <td>{{ $item->descricao }}</td>
          <td class="center">{{ $item->unidade ?? '—' }}</td>
          <td class="right">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
          <td class="right">{{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
          <td class="right">{{ number_format($item->valor_total, 2, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="center">Nenhum item cadastrado.</td></tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <th colspan="5" class="right">Valor Total</th>
        <th class="right">{{ $nota->valor_total_formatado }}</th>
      </tr>
    </tfoot>
  </table>

  <p style="margin-top: 30px;">
    <strong>Valor por extenso:</strong><br>
    {{ $nota->valor_extenso ?? '—' }}
  </p>

  <p style="margin-top: 40px; text-align: center;">
    <strong>Belém (PA), {{ now()->format('d/m/Y') }}</strong>
  </p>

  <table class="no-border" style="margin-top: 60px;">
    <tr>
      <td class="no-border center" style="width:50%;">
        ___________________________________________<br>
        <strong>Fiscal do Contrato</strong><br>
        Matrícula / Cargo
      </td>
      <td class="no-border center" style="width:50%;">
        ___________________________________________<br>
        <strong>Gestor do Contrato</strong><br>
        Matrícula / Cargo
      </td>
    </tr>
  </table>

  <footer>
    Sistema Fiscalizer • SEDUC-PA — Documento gerado em {{ now()->format('d/m/Y H:i') }}
  </footer>
</body>
</html>
