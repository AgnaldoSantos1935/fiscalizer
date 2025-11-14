<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Relatório Geral do Projeto</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
    h1,h2,h3 { margin:0 0 6px 0; }
    table { width:100%; border-collapse:collapse; margin-bottom:10px; }
    th,td { border:1px solid #ccc; padding:4px; }
    th { background:#eee; }
  </style>
</head>
<body>

<h1>Relatório Geral do Projeto</h1>
<h2>{{ $projeto->nome }}</h2>
<p><strong>Contrato:</strong> {{ $projeto->contrato->numero ?? '—' }}</p>
<p><strong>Status:</strong> {{ $projeto->status }}</p>
<p><strong>Período:</strong> {{ $projeto->data_inicio }} a {{ $projeto->data_fim }}</p>
<hr>

<h3>APFs</h3>
<table>
  <tr>
    <th>ID</th>
    <th>Total PF</th>
    <th>Observação</th>
  </tr>
  @foreach($projeto->apfs as $apf)
    <tr>
      <td>{{ $apf->id }}</td>
      <td>{{ $apf->total_pf }}</td>
      <td>{{ $apf->observacao }}</td>
    </tr>
  @endforeach
</table>

<h3>Atividades Técnicas</h3>
<table>
  <tr>
    <th>Data</th>
    <th>Etapa</th>
    <th>Analista</th>
    <th>Horas</th>
  </tr>
  @foreach($projeto->atividades as $a)
    <tr>
      <td>{{ $a->data }}</td>
      <td>{{ $a->etapa }}</td>
      <td>{{ $a->analista }}</td>
      <td>{{ $a->horas }}</td>
    </tr>
  @endforeach
</table>

<h3>Boletins de Medição</h3>
<table>
  <tr>
    <th>ID</th>
    <th>Data Emissão</th>
    <th>PF</th>
    <th>UST</th>
    <th>Valor</th>
  </tr>
  @foreach($projeto->boletins as $b)
    <tr>
      <td>{{ $b->id }}</td>
      <td>{{ $b->data_emissao }}</td>
      <td>{{ $b->total_pf }}</td>
      <td>{{ $b->total_ust }}</td>
      <td>R$ {{ number_format($b->valor_total,2,',','.') }}</td>
    </tr>
  @endforeach
</table>

</body>
</html>
