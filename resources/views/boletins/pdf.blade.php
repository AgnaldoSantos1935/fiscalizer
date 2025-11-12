<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Boletim de Medição Nº {{ $boletim->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }

        header {
            text-align: center;
            border-bottom: 2px solid #333;
            margin-bottom: 15px;
            padding-bottom: 8px;
        }

        header img {
            height: 70px;
        }

        h1 { font-size: 18px; margin: 0; }
        h2 { font-size: 15px; margin-top: 5px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #555;
            padding: 6px;
            text-align: left;
        }

        th { background: #e9ecef; }
        .text-right { text-align: right; }
        .total { font-weight: bold; background: #f8f9fa; }

        .grafico {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .assinaturas {
            margin-top: 50px;
        }

        .assinatura {
            width: 30%;
            display: inline-block;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 4px;
            margin: 0 10px;
        }

        footer {
            position: fixed;
            bottom: 30px;
            width: 100%;
            text-align: center;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('images/brasao_pa.png') }}" alt="Brasão do Pará"><br>
    <h1>GOVERNO DO ESTADO DO PARÁ</h1>
    <h2>SECRETARIA DE ESTADO DE EDUCAÇÃO – SEDUC/PA</h2>
    <h3>Diretoria de Tecnologia – DETec</h3>
    <h2 style="margin-top: 10px;">Boletim de Medição de Serviços de TI</h2>
</header>

<section>
    <p><strong>Nº do Boletim:</strong> {{ $boletim->id }}</p>
    <p><strong>Projeto:</strong> {{ $boletim->projeto->nome ?? '—' }}</p>
    <p><strong>Medição:</strong> {{ $boletim->medicao->mes_referencia ?? '—' }} |
        <strong>Contrato:</strong> {{ $boletim->medicao->contrato->numero ?? '—' }}</p>
    <p><strong>Data de Emissão:</strong> {{ $boletim->data_emissao->format('d/m/Y') }}</p>
</section>

<table>
    <thead>
        <tr>
            <th>Descrição / Item</th>
            <th>Pontos de Função (PF)</th>
            <th>UST</th>
            <th>Valor (R$)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $labels = [];
            $pfData = [];
            $ustData = [];
        @endphp

        @forelse($boletim->medicao->itens->where('projeto_id', $boletim->projeto_id) as $item)
            <tr>
                <td>{{ $item->descricao ?? '—' }}</td>
                <td class="text-right">{{ number_format($item->pontos_funcao, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->ust, 2, ',', '.') }}</td>
                <td class="text-right">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
            </tr>
            @php
                $labels[] = $item->descricao ?? 'Item';
                $pfData[] = $item->pontos_funcao ?? 0;
                $ustData[] = $item->ust ?? 0;
            @endphp
        @empty
            <tr><td colspan="4" class="text-center">Nenhum item encontrado.</td></tr>
        @endforelse

        <tr class="total">
            <td><strong>TOTAIS</strong></td>
            <td class="text-right">{{ number_format($boletim->total_pf, 2, ',', '.') }}</td>
            <td class="text-right">{{ number_format($boletim->total_ust, 2, ',', '.') }}</td>
            <td class="text-right">R$ {{ number_format($boletim->valor_total, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

@if(!empty($boletim->observacao))
    <p><strong>Observações:</strong> {{ $boletim->observacao }}</p>
@endif

{{-- GRÁFICOS --}}
<div class="grafico">
    <img src="data:image/png;base64,{{ base64_encode(
        \QuickChart::chart()
            ->width(800)
            ->height(300)
            ->backgroundColor('white')
            ->format('png')
            ->config([
                'type' => 'bar',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Pontos de Função (PF)',
                            'backgroundColor' => '#007bff',
                            'data' => $pfData
                        ],
                        [
                            'label' => 'UST',
                            'backgroundColor' => '#28a745',
                            'data' => $ustData
                        ]
                    ],
                ],
                'options' => [
                    'plugins' => ['legend' => ['position' => 'bottom']],
                    'scales' => [
                        'x' => ['ticks' => ['autoSkip' => false, 'maxRotation' => 45, 'minRotation' => 45]],
                        'y' => ['beginAtZero' => true]
                    ]
                ]
            ])
            ->toBinary()
    ) }}" alt="Gráfico de Pontos e UST">

    <img src="data:image/png;base64,{{ base64_encode(
        \QuickChart::chart()
            ->width(400)
            ->height(300)
            ->backgroundColor('white')
            ->format('png')
            ->config([
                'type' => 'pie',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [[
                        'label' => 'Distribuição de PF',
                        'data' => $pfData,
                        'backgroundColor' => [
                            '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                            '#6f42c1', '#20c997', '#fd7e14', '#343a40', '#adb5bd'
                        ]
                    ]]
                ],
                'options' => [
                    'plugins' => ['legend' => ['position' => 'right']],
                ]
            ])
            ->toBinary()
    ) }}" alt="Distribuição de PF">
</div>

<div class="assinaturas">
    <div class="assinatura">
        <strong>Fiscal Técnico</strong><br>
        Nome: _______________________________________<br>
        Matrícula: ___________________
    </div>

    <div class="assinatura">
        <strong>Fiscal Administrativo</strong><br>
        Nome: _______________________________________<br>
        Matrícula: ___________________
    </div>

    <div class="assinatura">
        <strong>Gestor do Contrato</strong><br>
        Nome: _______________________________________<br>
        Matrícula: ___________________
    </div>
</div>

<footer>
    Sistema Fiscalizer – SEDUC/PA | Documento gerado em {{ now()->format('d/m/Y H:i') }}
</footer>

</body>
</html>
