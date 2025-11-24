<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Pretensão de Empenho — {{ $empenho->numero }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; margin: 40px 50px; color: #222; }
    header { text-align: center; border-bottom: 2px solid #0a472e; margin-bottom: 20px; padding-bottom: 10px; }
    header h1 { font-size: 15pt; margin: 0; color: #0a472e; text-transform: uppercase; }
    header h2 { font-size: 11pt; margin: 0; color: #555; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px; }
    th { background-color: #f5f5f5; font-weight: bold; text-align: center; }
    .right { text-align: right; }
    .center { text-align: center; }
    .no-border { border: none !important; }
    footer { position: fixed; bottom: 30px; left: 0; right: 0; text-align: center; font-size: 9pt; color: #777; }
  </style>
  </head>
<body>
  <header>
    <h1>Secretaria de Estado de Educação — SEDUC/PA</h1>
    <h2>Sistema Fiscalizer • Pretensão de Empenho</h2>
  </header>

  <h3 style="text-align:center; text-decoration: underline; margin-top:0;">Pretensão de Empenho Nº {{ $empenho->numero }}</h3>
  <p class="center" style="margin-top: 5px;">Referente ao mês de <strong>{{ $mesNome }}</strong> de <strong>{{ $ano }}</strong></p>

  <table class="no-border" style="margin-bottom: 20px;">
    <tr>
      <td class="no-border"><strong>Empresa:</strong> {{ $empenho->empresa->razao_social ?? '—' }}</td>
      <td class="no-border"><strong>CNPJ:</strong> {{ $empenho->empresa->cnpj ?? '—' }}</td>
    </tr>
    <tr>
      <td class="no-border"><strong>Contrato:</strong> {{ $empenho->contrato->numero ?? '—' }}</td>
      <td class="no-border"><strong>Data de Lançamento:</strong> {{ $empenho->data_formatada ?? '—' }}</td>
    </tr>
  </table>

  <h4 style="margin-top: 10px; color: #0a472e;">Resumo</h4>
  <table>
    <thead>
      <tr>
        <th>Objeto</th>
        <th style="width: 20%">Valor Total</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Solicitação de empenho mensal para execução contratual referente a {{ $mesNome }}/{{ $ano }}.</td>
        <td class="right">{{ $empenho->valor_total_formatado ?? '—' }}</td>
      </tr>
    </tbody>
  </table>

  <p style="margin-top: 20px;">Este documento é submetido à aprovação do Gestor do Contrato, para autorização do prosseguimento do fluxo de empenho no mês indicado.</p>

  <table class="no-border" style="margin-top: 40px;">
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