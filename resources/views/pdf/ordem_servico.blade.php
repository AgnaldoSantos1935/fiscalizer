<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 30px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 90px;
        }

        .titulo-orgao {
            font-size: 14px;
            text-transform: uppercase;
            margin-top: 5px;
            font-weight: bold;
        }

        .titulo-documento {
            font-size: 16px;
            margin-top: 10px;
            font-weight: bold;
        }

        .secao-titulo {
            font-size: 13px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 6px;
            border-bottom: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table, tr, td, th {
            border: 1px solid #000;
        }

        th, td {
            padding: 5px;
        }

        .assinaturas {
            margin-top: 50px;
            width: 100%;
        }

        .assinaturas td {
            height: 70px;
            text-align: center;
            vertical-align: bottom;
            border: none !important;
        }

        .rodape {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('images/brasao-pa.png') }}" alt="Brasão do Pará">

    <div class="titulo-orgao">
        Governo do Estado do Pará<br>
        Secretaria de Estado de Educação – SEDUC/PA<br>
        Diretoria de Tecnologia – DETEC
    </div>

    <div class="titulo-documento">
        ORDEM DE SERVIÇO Nº {{ $os->numero_os }}
    </div>
</div>

{{-- Seção 1 --}}
<div class="secao-titulo">1. Identificação da Demanda</div>
<p><strong>Nº da Demanda:</strong> {{ $demanda->id }}</p>
<p><strong>Título:</strong> {{ $demanda->titulo }}</p>
<p><strong>Tipo de Manutenção:</strong> {{ $demanda->tipo_manutencao }}</p>
<p><strong>Sistema:</strong> {{ optional($demanda->sistema)->nome ?? '—' }}</p>
<p><strong>Módulo:</strong> {{ optional($demanda->modulo)->nome ?? '—' }}</p>

{{-- Seção 2 --}}
<div class="secao-titulo">2. Requisitos Aprovados</div>

@if($requisitos && count($requisitos))
<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Título</th>
            <th>Descrição</th>
            <th>PF</th>
            <th>UST</th>
            <th>Complexidade</th>
        </tr>
    </thead>
    <tbody>
        @foreach($requisitos as $r)
        <tr>
            <td>{{ $r['codigo'] }}</td>
            <td>{{ $r['titulo'] }}</td>
            <td>{{ $r['descricao'] ?? '—' }}</td>
            <td>{{ $r['pontos_de_funcao'] }}</td>
            <td>{{ $r['ust'] }}</td>
            <td>{{ $r['complexidade'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>Nenhum requisito estruturado encontrado.</p>
@endif

{{-- Seção 3 --}}
<div class="secao-titulo">3. Estimativa de Esforço</div>
<p><strong>Total PF:</strong> {{ $os->pf_total }}</p>
<p><strong>Total UST:</strong> {{ $os->ust_total }}</p>

{{-- Seção 4 --}}
<div class="secao-titulo">4. Cronograma Aprovado</div>

@if($cronograma && count($cronograma))
<table>
    <thead>
        <tr>
            <th>Marco</th>
            <th>Início</th>
            <th>Fim</th>
            <th>Duração</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cronograma as $c)
        <tr>
            <td>{{ $c['marco'] }}</td>
            <td>{{ $c['inicio'] }}</td>
            <td>{{ $c['fim'] }}</td>
            <td>{{ $c['duracao_dias'] }} dias</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>Cronograma não informado.</p>
@endif

{{-- Seção 5 --}}
<div class="secao-titulo">5. Observações Gerais</div>
<p style="font-size: 11px;">
    Esta Ordem de Serviço foi gerada automaticamente pelo Sistema Fiscalizer
    a partir do Documento Técnico enviado pela empresa contratada e validado
    pela Diretoria de Tecnologia (DETEC – SEDUC/PA).
</p>

{{-- Assinaturas --}}
<table class="assinaturas">
    <tr>
        <td>
            _______________________________________________<br>
            Responsável Técnico – DETEC/SEDUC-PA
        </td>
        <td>
            _______________________________________________<br>
            Gestor do Contrato – SEDUC-PA
        </td>
    </tr>
</table>

<div class="rodape">
    Documento gerado automaticamente. Qualquer alteração deve ser registrada em processo eletrônico.
</div>
<div style="text-align: right;">
    <img src="data:image/png;base64,{{ $qr_code_base64 }}" width="120">
    <p class="small">Verifique autenticidade: {{ $os->verificacao_url }}</p>
</div>

</body>
</html>
