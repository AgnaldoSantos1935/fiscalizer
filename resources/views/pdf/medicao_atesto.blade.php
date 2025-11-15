<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 30px; }
        .header { text-align: center; margin-bottom: 20px; }
        .secao { margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #000; padding: 4px; }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('images/brasao-pa.png') }}" width="80"><br>
    <strong>GOVERNO DO ESTADO DO PARÁ</strong><br>
    SECRETARIA DE ESTADO DE EDUCAÇÃO – SEDUC/PA<br>
    DIRETORIA DE TECNOLOGIA – DETEC<br><br>
    <strong>ATESTO DE MEDIÇÃO Nº {{ $medicao->id }}</strong><br>
    Competência: {{ $medicao->competencia }}
</div>

<div class="secao">
    <strong>Contrato:</strong> {{ $medicao->contrato->numero ?? '—' }}<br>
    <strong>Objeto:</strong> {{ $medicao->contrato->objeto_resumido ?? Str::limit($medicao->contrato->objeto, 150) }}<br>
    <strong>Tipo de Medição:</strong> {{ strtoupper($medicao->tipo) }}
</div>

<div class="secao">
    <strong>Resumo Financeiro</strong><br>
    Valor bruto: R$ {{ number_format($medicao->valor_bruto ?? 0, 2, ',', '.') }}<br>
    Descontos: R$ {{ number_format($medicao->valor_desconto ?? 0, 2, ',', '.') }}<br>
    Valor líquido: <strong>R$ {{ number_format($medicao->valor_liquido ?? 0, 2, ',', '.') }}</strong>
</div>

@if($medicao->tipo === 'software')
<div class="secao">
    <strong>Itens (Software – PF/UST)</strong>
    <table>
        <thead>
            <tr>
                <th>Descrição</th><th>PF</th><th>UST</th><th>Horas</th><th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicao->itensSoftware as $i)
            <tr>
                <td>{{ $i->descricao }}</td>
                <td>{{ $i->pf }}</td>
                <td>{{ $i->ust }}</td>
                <td>{{ $i->horas }}</td>
                <td>R$ {{ number_format($i->valor_total ?? 0, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($medicao->tipo === 'telco')
<div class="secao">
    <strong>Resumo SLA Telecom</strong><br>
    SLA contratado: {{ $medicao->sla_contratado }}%<br>
    SLA alcançado: {{ $medicao->sla_alcancado }}%<br>
</div>
@endif

@if($medicao->inconsistencias_json)
<div class="secao">
    <strong>Inconsistências / Observações</strong>
    <ul>
        @foreach(json_decode($medicao->inconsistencias_json, true) as $inc)
        <li>{{ $inc }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="secao" style="margin-top:40px;">
    Declaro, na qualidade de fiscal técnico, que os serviços/fornecimentos foram
    verificados e estão em conformidade com o contrato, salvo as observações acima registradas.
</div>

<table style="margin-top:60px; border:0;">
    <tr>
        <td style="border:0; text-align:center;">
            ___________________________________________<br>
            Fiscal Técnico
        </td>
        <td style="border:0; text-align:center;">
            ___________________________________________<br>
            Fiscal Administrativo / Gestor
        </td>
    </tr>
</table>

</body>
</html>
