<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Termo de Referência - {{ $tr->titulo }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        h1 { font-size: 18px; }
        h2 { font-size: 16px; border-bottom: 1px solid #999; padding-bottom: 4px; margin-top: 16px; }
        .meta { margin-bottom: 12px; }
        .meta div { margin: 2px 0; }
        .section { margin-top: 10px; }
        .section .content { margin-top: 6px; }
        .content { line-height: 1.4; }
    </style>
</head>
<body>
    @php
        // Soma o total dos itens para uso nas seções de resumo e rodapé da tabela
        $totalItens = $tr->itens->sum('valor_total');
    @endphp
    <h1>Termo de Referência</h1>
    <div class="meta">
        <div><strong>Título:</strong> {{ $tr->titulo }}</div>
        <div><strong>Tipo:</strong> {{ $tr->tipo_tr === 'bens_comuns' ? 'Bens Comuns' : ($tr->tipo_tr === 'servicos_sem_mao_de_obra_sem_prorrogacao' ? 'Serviços sem Mão-de-Obra e sem Prorrogação' : '—') }}</div>
        <div><strong>PAE nº:</strong> {{ $tr->pae_numero ?? '—' }}</div>
        <div><strong>Cidade:</strong> {{ $tr->cidade ?? '—' }}</div>
        <div><strong>Data de Emissão:</strong> {{ optional($tr->data_emissao)->format('d/m/Y') }}</div>
        <div><strong>Responsável:</strong> {{ $tr->responsavel_nome ?? '—' }} ({{ $tr->responsavel_cargo ?? '—' }}) - Matrícula {{ $tr->responsavel_matricula ?? '—' }}</div>
    </div>

    <div class="section">
        <h2>Objeto</h2>
        <div class="content">{!! $tr->objeto !!}</div>
    </div>
    <div class="section">
        <h2>Justificativa</h2>
        <div class="content">{!! $tr->justificativa !!}</div>
    </div>
    <div class="section">
        <h2>Escopo</h2>
        <div class="content">{!! $tr->escopo !!}</div>
    </div>
    <div class="section">
        <h2>Requisitos</h2>
        <div class="content">{!! $tr->requisitos !!}</div>
    </div>
    <div class="section">
        <h2>Critérios de Julgamento</h2>
        <div class="content">
            @php
                $mapCrit = [
                    'menor_preco' => 'Menor Preço',
                    'maior_desconto' => 'Maior Desconto',
                ];
            @endphp
            <strong>Critério de seleção:</strong>
            {{ $mapCrit[$tr->criterio_julgamento_tipo] ?? '—' }}
            @if(!empty($tr->criterios_julgamento))
                <div class="content">{!! $tr->criterios_julgamento !!}</div>
            @endif
        </div>
    </div>
    <div class="section">
        <h2>Prazos</h2>
        <div class="content">{!! $tr->prazos !!}</div>
    </div>
    <div class="section">
        <h2>Local de Execução</h2>
        <div class="content">{!! $tr->local_execucao !!}</div>
    </div>
    <div class="section">
        <h2>Especificação do Produto</h2>
        <div class="content">{!! $tr->especificacao_produto !!}</div>
    </div>
    <div class="section">
        <h2>Locais de Entrega e Regras de Recebimento</h2>
        <div class="content">{!! $tr->locais_entrega_recebimento !!}</div>
    </div>
    <div class="section">
        <h2>Garantia, Manutenção e Assistência Técnica</h2>
        <div class="content">{!! $tr->garantia_manutencao_assistencia !!}</div>
    </div>
    <div class="section">
        <h2>5 - Qualidade, Amostra, Garantia e Assistência (ref. 5.1–5.4)</h2>
        <div class="content">
            <p><strong>5.1 Prova de Qualidade:</strong>
                @if($tr->prova_qualidade)
                    Sim. {!! $tr->prova_qualidade_justificativa ?? '' !!}
                @else
                    Não.
                @endif
            </p>
            <p><strong>5.2 Amostra:</strong>
                @if($tr->edital_exigira_amostra)
                    Sim. {!! $tr->edital_amostra_justificativa ?? '' !!}
                @else
                    Não.
                @endif
            </p>
            <p><strong>5.3 Garantia do Bem:</strong>
                @if($tr->garantia_bem)
                    Sim. {!! $tr->garantia_bem_itens ?? '' !!}@if(!is_null($tr->garantia_bem_meses)) — Garantia de {{ $tr->garantia_bem_meses }} meses.@endif
                @else
                    Não.
                @endif
            </p>
            <p><strong>5.4 Assistência Técnica:</strong>
                @php
                    $mapAT = [ 'credenciada' => 'Credenciada', 'propria' => 'Própria', 'nao' => 'Não' ];
                @endphp
                {{ $mapAT[$tr->assistencia_tecnica_tipo] ?? '—' }}@if(!is_null($tr->assistencia_tecnica_meses) && $tr->assistencia_tecnica_tipo !== 'nao') — {{ $tr->assistencia_tecnica_meses }} meses.@endif
            </p>
        </div>
    </div>
    <div class="section">
        <h2>6 - Contratação, Julgamento e ME/EPP (ref. 6.1–6.5)</h2>
        <div class="content">
            <p><strong>6.1 Forma de Contratação:</strong>
                @php
                    $mapFC = [
                        'inexigibilidade_art74_y' => 'Inexigibilidade (art. 74, inciso correspondente)',
                        'dispensa_valor_art75_ii' => 'Dispensa por valor (art. 75, II)',
                        'dispensa_art75_y' => 'Dispensa (art. 75, inciso correspondente)',
                        'pregao_eletronico' => 'Pregão Eletrônico',
                        'concorrencia' => 'Concorrência',
                    ];
                @endphp
                {{ $mapFC[$tr->forma_contratacao] ?? '—' }}
            </p>
            <p><strong>6.3 Orçamento Sigiloso:</strong>
                @if($tr->orcamento_sigiloso)
                    Sim. {!! $tr->orcamento_sigiloso_justificativa ?? '' !!}
                @else
                    Não.
                @endif
            </p>
            <p><strong>6.5 Itens Exclusivos ME/EPP:</strong>
                @if($tr->itens_exclusivos_me_epp)
                    Sim. {!! $tr->itens_exclusivos_lista ?? '' !!}
                @else
                    Não.
                @endif
            </p>
        </div>
    </div>
    <div class="section">
        <h2>7 - Habilitação, Sustentabilidade e Riscos (ref. 7.1–7.5)</h2>
        <div class="content">
            <p><strong>7.1 Habilitação Jurídica:</strong>
                Exigência de existência: {{ $tr->habilitacao_juridica_existencia ? 'Sim' : 'Não' }}; Autorização: {{ $tr->habilitacao_juridica_autorizacao ? 'Sim' : 'Não' }}.
            </p>
            <p><strong>7.2 Habilitação Técnica:</strong>
                @if($tr->habilitacao_tecnica_exigida)
                    Sim. {!! $tr->habilitacao_tecnica_qual ?? '' !!} {!! $tr->habilitacao_tecnica_justificativa ?? '' !!}
                @else
                    Não.
                @endif
            </p>
            <p><strong>7.3 Qualificações Técnicas Exigidas:</strong>
                Declaração de ciência: {{ $tr->qt_declaracao_ciencia ? 'Sim' : 'Não' }}; Registro em entidade: {{ $tr->qt_registro_entidade ? 'Sim' : 'Não' }}; Indicação de pessoal: {{ $tr->qt_indicacao_pessoal ? 'Sim' : 'Não' }}.
                @if($tr->qt_outro)
                    Outro: {!! $tr->qt_outro_especificar ?? '' !!} {!! $tr->qt_outro_justificativa ?? '' !!}
                @endif
                @if($tr->qt_nao_exigida)
                    Não exigida.
                @endif
                @if(!empty($tr->qt_declaracao_justificativa) || !empty($tr->qt_registro_justificativa) || !empty($tr->qt_indicacao_justificativa))
                    {!! $tr->qt_declaracao_justificativa ?? '' !!} {!! $tr->qt_registro_justificativa ?? '' !!} {!! $tr->qt_indicacao_justificativa ?? '' !!}
                @endif
            </p>
            <p><strong>7.4 Sustentabilidade:</strong>
                @if($tr->criterio_sustentabilidade)
                    Sim. {!! $tr->criterio_sustentabilidade_especificar ?? '' !!}
                @else
                    Não.
                @endif
            </p>
            <p><strong>7.5 Riscos Assumidos pela Contratada:</strong>
                @if($tr->riscos_assumidos_contratada)
                    Sim. {!! $tr->riscos_assumidos_especificar ?? '' !!}
                @else
                    Não.
                @endif
            </p>
        </div>
    </div>
    <div class="section">
        <h2>8 - Entrega e Recebimento do Bem (ref. 8.1–8.3)</h2>
        <div class="content">
            <p><strong>8.1 Forma de Entrega:</strong>
                @if($tr->entrega_forma === 'total')
                    Total, de uma só vez.
                @elseif($tr->entrega_forma === 'parcelada')
                    Parcelada — @if(!is_null($tr->entrega_parcelas_quantidade)) {{ $tr->entrega_parcelas_quantidade }} parcelas; @endif @if(!is_null($tr->entrega_primeira_em_dias)) 1ª em até {{ $tr->entrega_primeira_em_dias }} dias; @endif @if(!is_null($tr->entrega_aviso_antecedencia_dias)) aviso com {{ $tr->entrega_aviso_antecedencia_dias }} dias de antecedência.@endif
                @else
                    —
                @endif
            </p>
            <p><strong>8.2 Recebimento:</strong>
                Endereço: {!! $tr->recebimento_endereco ?? '—' !!}; Horário: {{ $tr->recebimento_horario ?? '—' }}.
            </p>
            <p><strong>8.3 Validade Mínima na Entrega:</strong>
                @if(!is_null($tr->validade_minima_entrega_dias))
                    {{ $tr->validade_minima_entrega_dias }} dias.
                @else
                    —
                @endif
            </p>
        </div>
    </div>
    <div class="section">
        <h2>9 - Prazo, Pagamento e Garantia do Contrato (ref. 9.1–9.4)</h2>
        <div class="content">
            <p><strong>9.1 Prazo do Contrato:</strong>
                @php
                    $mapPrazo = [ '30_dias' => '30 dias (pronta entrega)', '12_meses' => '12 meses' ];
                @endphp
                {{ $mapPrazo[$tr->prazo_contrato] ?? '—' }}
            </p>
            <p><strong>9.2 Possibilidade de Prorrogação:</strong>
                {{ $tr->prorrogacao_possivel ? 'Sim (art. 111)' : 'Não' }}
            </p>
            <p><strong>9.3 Forma de Pagamento:</strong>
                Meio: {{ $tr->pagamento_meio === 'ordem_bancaria' ? 'Ordem bancária' : '—' }}; Onde: {{ $tr->pagamento_onde ?? '—' }}; Prazo: @if(!is_null($tr->pagamento_prazo_dias)) até {{ $tr->pagamento_prazo_dias }} dias corridos @else — @endif; Prova de regularidade fiscal: @php $mapReg = ['sicaf_ou_cul' => 'SICAF/CUL', 'art68_documentos' => 'Art. 68 (documentos)']; @endphp {{ $mapReg[$tr->regularidade_fiscal_prova_tipo] ?? '—' }}.
            </p>
            <p><strong>9.4 Garantia do Contrato:</strong>
                @if($tr->garantia_contrato_tipo === 'percentual')
                    {{ number_format($tr->garantia_contrato_percentual ?? 0, 2, ',', '.') }}% do valor inicial. {!! $tr->garantia_contrato_justificativa ?? '' !!}
                @elseif($tr->garantia_contrato_tipo === 'nao_ha')
                    Não há. {!! $tr->garantia_contrato_justificativa ?? '' !!}
                @else
                    —
                @endif
            </p>
        </div>
    </div>
    <div class="section">
        <h2>Dados Orçamentários da Contratação (ref. 10.1)</h2>
        <div class="content">
            <p><strong>Funcional Programática:</strong> {{ $tr->funcional_programatica ?? '—' }}</p>
            <p><strong>Elemento de Despesa:</strong> {{ $tr->elemento_despesa ?? '—' }}</p>
            <p><strong>Fonte do Recurso:</strong> {{ $tr->fonte_recurso ?? '—' }}</p>
        </div>
    </div>
    <div class="section">
        <h2>Critérios de Medição e Pagamento</h2>
        <div class="content">{!! $tr->criterios_medicao_pagamento !!}</div>
    </div>
    <div class="section">
        <h2>Forma e Critérios de Seleção do Fornecedor</h2>
        <div class="content">{!! $tr->forma_criterios_selecao_fornecedor !!}</div>
    </div>
    <div class="section">
        <h2>Estimativas e Adequação Orçamentária</h2>
        <div class="content">
            @if(!empty($tr->estimativas_valor_texto)) {!! $tr->estimativas_valor_texto !!} @endif
            @if(!empty($tr->adequacao_orcamentaria)) {!! $tr->adequacao_orcamentaria !!} @endif
            @if(!is_null($tr->adequacao_orcamentaria_confirmada)) Adequação confirmada: {{ $tr->adequacao_orcamentaria_confirmada ? 'Sim' : 'Não' }}. @endif
        </div>
    </div>
    <div class="section">
        <h2>Forma de Pagamento</h2>
        <div class="content">{!! $tr->forma_pagamento !!}</div>
    </div>
    <div class="section">
        <h2>Itens do Termo de Referência</h2>
        @php $totalItens = $tr->itens->sum('valor_total'); @endphp
        <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse: collapse; margin-top: 8px;">
            <thead>
                <tr style="background:#eee;">
                    <th align="left">Descrição</th>
                    <th align="left">Unidade</th>
                    <th align="right">Quantidade</th>
                    <th align="right">Valor Unitário (R$)</th>
                    <th align="right">Valor Total (R$)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tr->itens as $item)
                    <tr>
                        <td>{{ $item->descricao }}</td>
                        <td>{{ $item->unidade ?? '—' }}</td>
                        <td align="right">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                        <td align="right">{{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                        <td align="right">{{ number_format($item->valor_total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Nenhum item cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" align="right">Total dos Itens</th>
                    <th align="right">{{ number_format($totalItens, 2, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="section">
        <h2>Resumo de Totais</h2>
        @php $estimado = $tr->valor_estimado ?? 0; $diff = $estimado - $totalItens; @endphp
        <table width="100%" border="0" cellspacing="0" cellpadding="4" style="margin-top: 8px;">
            <tr>
                <td align="left"><strong>Total dos Itens</strong></td>
                <td align="right">R$ {{ number_format($totalItens, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td align="left"><strong>Valor Estimado (cabeçalho)</strong></td>
                <td align="right">R$ {{ number_format($estimado, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td align="left"><strong>Diferença (Estimado − Itens)</strong></td>
                <td align="right">R$ {{ number_format($diff, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>