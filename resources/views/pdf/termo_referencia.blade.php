@extends('pdf.layouts.base')

@section('title') TERMO DE REFERÊNCIA @endsection

@section('styles')
/* SEDUC/PA — Times New Roman, tamanhos e espaçamentos */
body { font-family: 'Times New Roman', serif; font-size: 11px; line-height: 1.15; }
h1 { font-size: 16px; text-transform: uppercase; text-align: center; }
h2 { font-size: 12px; text-align: center; }
h3 { font-size: 11px; text-transform: uppercase; }
p, li { font-size: 11px; text-align: justify; }
table { width: 100%; border-collapse: collapse; page-break-inside: auto; }
th, td { border: 1px solid #000; padding: 6px; }
th { background: #eeeeee; font-weight: bold; }
.num { text-align: right; }
.no-break { page-break-inside: avoid; }
.table-items td.num { white-space: nowrap; }
.table-items th:nth-child(1), .table-items td:nth-child(1) { width: 7%; }
.table-items th:nth-child(2), .table-items td:nth-child(2) { width: 7%; }
.table-items th:nth-child(3), .table-items td:nth-child(3) { width: 34%; }
.table-items th:nth-child(4), .table-items td:nth-child(4) { width: 10%; }
.table-items th:nth-child(5), .table-items td:nth-child(5) { width: 10%; }
.table-items th:nth-child(6), .table-items td:nth-child(6) { width: 12%; }
.table-items th:nth-child(7), .table-items td:nth-child(7) { width: 12%; }
.table-items th:nth-child(8), .table-items td:nth-child(8) { width: 8%; }
.assinaturas { margin-top: 32px; }
.assin { text-align: center; margin-top: 40px; }
.linha-assin { border-top: 1px solid #000; width: 60%; margin: 0 auto 4px auto; }
@endsection

@section('header')
<div class="row" style="justify-content: center; text-align: center; position: relative;">
    <div>
        <div style="font-weight:bold; text-transform:uppercase;">GOVERNO DO ESTADO DO PARÁ</div>
        <div style="font-weight:bold; text-transform:uppercase;">SECRETARIA DE ESTADO DE EDUCAÇÃO</div>
        <div style="text-transform:uppercase;">DIRETORIA DE RECURSOS TECNOLÓGICOS</div>
        <div style="text-transform:uppercase;">COORDENADORIA DE INFRAESTRUTURA TECNOLÓGICA</div>
    </div>
    <div style="position:absolute; right:40px; top:0; font-size:10px;">Gerado em {{ now()->format('d/m/Y H:i') }}</div>
</div>
@endsection

@section('content')
    <h1>TERMO DE REFERÊNCIA</h1>
    <h2>TERMO DE REFERÊNCIA DE BENS COMUNS</h2>

    <h3>DO OBJETO</h3>
    <p>{{ $sec_do_objeto ?? '' }}</p>

    <h3>O QUE SERÁ CONTRATADO?</h3>
    <p>{{ $sec_o_que_sera_contratado ?? '' }}</p>

    <h3>QUAL O MOTIVO DA CONTRATAÇÃO</h3>
    <p>{{ $sec_qual_o_motivo_da_contratacao ?? '' }}</p>

    <h3>RESULTADOS ESPERADOS</h3>
    <p>{{ $sec_resultados_esperados ?? '' }}</p>

    <h3>FUNDAMENTAÇÃO LEGAL</h3>
    <p>{{ $sec_fundamentacao_legal ?? '' }}</p>

    <h3>NATUREZA E GARANTIA DO SERVIÇO</h3>
    <p>{{ $sec_natureza_e_garantia_do_servico ?? '' }}</p>

    <h3>CRITÉRIOS DE SELEÇÃO</h3>
    <p>{{ $sec_criterios_de_selecao ?? '' }}</p>

    <h3>REQUISITOS DA CONTRATADA</h3>
    <p>{{ $sec_requisitos_da_contratada ?? '' }}</p>

    <h3>DAS OBRIGAÇÕES DAS PARTES</h3>
    <p>{{ $sec_das_obrigacoes_das_partes ?? '' }}</p>

    <h3>PRAZO, FORMA DE PAGAMENTO E GARANTIA DO CONTRATO</h3>
    <p>{{ $sec_prazo_forma_de_pagamento_e_garantia_do_contrato ?? '' }}</p>

    <h3>GESTÃO E FISCALIZAÇÃO DO CONTRATO</h3>
    <p>{{ $sec_gestao_e_fiscalizacao_do_contrato ?? '' }}</p>

    <h3>PENALIDADES</h3>
    <p>{{ $sec_penalidades ?? '' }}</p>

    <h3>PREVISÃO ORÇAMENTÁRIA</h3>
    <p>{{ $sec_previsao_orcamentaria ?? '' }}</p>

    <h3>ANEXO I</h3>
    <p>{{ $sec_anexo_i ?? '' }}</p>

    <h3>ESPECIFICAÇÕES TÉCNICAS</h3>
    <p>{{ $sec_especificacoes_tecnicas ?? '' }}</p>

    <h3>ITENS</h3>
    <table class="table-items">
        <thead>
            <tr>
                <th>Lote</th>
                <th>Item</th>
                <th>Descrição</th>
                <th>Unidade</th>
                <th class="num">Quantidade</th>
                <th class="num">Valor Unitário</th>
                <th class="num">Valor Total</th>
                <th>Código SIMAS</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($itens ?? []) as $i)
                <tr class="no-break">
                    <td>{{ $i['lote'] ?? '' }}</td>
                    <td>{{ $i['item'] ?? '' }}</td>
                    <td>{{ $i['descricao'] ?? '' }}</td>
                    <td>{{ $i['unidade'] ?? '' }}</td>
                    <td class="num">{{ isset($i['quantidade']) ? number_format((float)$i['quantidade'], 0, ',', '.') : '' }}</td>
                    <td class="num">{{ isset($i['valor_unitario']) ? 'R$ ' . number_format((float)$i['valor_unitario'], 2, ',', '.') : '' }}</td>
                    <td class="num">{{ isset($i['valor_total']) ? 'R$ ' . number_format((float)$i['valor_total'], 2, ',', '.') : '' }}</td>
                    <td>{{ $i['codigo_simas'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="assinaturas no-break">
        <div class="assin">
            <div class="linha-assin"></div>
            <div><strong>{{ $assin_elaboracao_nome ?? '' }}</strong></div>
            <div>{{ $assin_elaboracao_cargo ?? '' }}</div>
        </div>
        <div class="assin">
            <div class="linha-assin"></div>
            <div><strong>{{ $assin_supervisor_nome ?? '' }}</strong></div>
            <div>{{ $assin_supervisor_cargo ?? '' }}</div>
        </div>
        <div class="assin">
            <div class="linha-assin"></div>
            <div><strong>{{ $assin_ordenador_nome ?? '' }}</strong></div>
            <div>{{ $assin_ordenador_cargo ?? '' }}</div>
        </div>
    </div>
@endsection