<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermoReferencia extends Model
{
    use HasFactory;

    protected $table = 'termos_referencia';

    protected $fillable = [
        'titulo',
        'tipo_tr',
        'pae_numero',
        'cidade',
        'data_emissao',
        'responsavel_nome',
        'responsavel_cargo',
        'responsavel_matricula',
        'objeto',
        'justificativa',
        'escopo',
        'requisitos',
        'criterios_julgamento',
        'prazos',
        'local_execucao',
        'forma_pagamento',
        // novos campos
        'modelo_execucao',
        'modelo_gestao',
        'criterios_medicao_pagamento',
        'forma_criterios_selecao_fornecedor',
        'especificacao_produto',
        'locais_entrega_recebimento',
        'garantia_manutencao_assistencia',
        'estimativas_valor_texto',
        'adequacao_orcamentaria',
        'valor_estimado',
        'status',
        // flags Sim/Não
        'garantia_exigida',
        'manutencao_incluida',
        'assistencia_tecnica_incluida',
        'adequacao_orcamentaria_confirmada',
        // 5.1 Prova de qualidade
        'prova_qualidade',
        'prova_qualidade_justificativa',
        // 5.2 Amostra
        'edital_exigira_amostra',
        'edital_amostra_justificativa',
        // 5.3 Garantia do bem
        'garantia_bem',
        'garantia_bem_itens',
        'garantia_bem_meses',
        // 5.4 Assistência técnica
        'assistencia_tecnica_tipo',
        'assistencia_tecnica_meses',
        // 6.1 Forma de contratação
        'forma_contratacao',
        // 6.2 Critério de julgamento (seleção)
        'criterio_julgamento_tipo',
        // 6.3 Orçamento sigiloso
        'orcamento_sigiloso',
        'orcamento_sigiloso_justificativa',
        // 6.5 Itens exclusivos ME/EPP
        'itens_exclusivos_me_epp',
        'itens_exclusivos_lista',
        // 7.x Habilitação/Qualificação/Sustentabilidade/Riscos
        'habilitacao_juridica_existencia',
        'habilitacao_juridica_autorizacao',
        'habilitacao_tecnica_exigida',
        'habilitacao_tecnica_qual',
        'habilitacao_tecnica_justificativa',
        'qt_declaracao_ciencia',
        'qt_declaracao_justificativa',
        'qt_registro_entidade',
        'qt_registro_justificativa',
        'qt_indicacao_pessoal',
        'qt_indicacao_justificativa',
        'qt_outro',
        'qt_outro_especificar',
        'qt_outro_justificativa',
        'qt_nao_exigida',
        'criterio_sustentabilidade',
        'criterio_sustentabilidade_especificar',
        'riscos_assumidos_contratada',
        'riscos_assumidos_especificar',
        // 8.x Entrega e recebimento
        'entrega_forma',
        'entrega_parcelas_quantidade',
        'entrega_primeira_em_dias',
        'entrega_aviso_antecedencia_dias',
        'recebimento_endereco',
        'recebimento_horario',
        'validade_minima_entrega_dias',
        // 9.x Prazo, pagamento e garantia
        'prazo_contrato',
        'prorrogacao_possivel',
        'pagamento_meio',
        'pagamento_onde',
        'pagamento_prazo_dias',
        'regularidade_fiscal_prova_tipo',
        'garantia_contrato_tipo',
        'garantia_contrato_percentual',
        'garantia_contrato_justificativa',
        // 10.x Dados orçamentários
        'funcional_programatica',
        'elemento_despesa',
        'fonte_recurso',
    ];

    protected $casts = [
        'valor_estimado' => 'decimal:2',
        'garantia_exigida' => 'boolean',
        'manutencao_incluida' => 'boolean',
        'assistencia_tecnica_incluida' => 'boolean',
        'adequacao_orcamentaria_confirmada' => 'boolean',
        'prova_qualidade' => 'boolean',
        'edital_exigira_amostra' => 'boolean',
        'garantia_bem' => 'boolean',
        'orcamento_sigiloso' => 'boolean',
        'itens_exclusivos_me_epp' => 'boolean',
        'garantia_bem_meses' => 'integer',
        'assistencia_tecnica_meses' => 'integer',
        // 7.x booleans
        'habilitacao_juridica_existencia' => 'boolean',
        'habilitacao_juridica_autorizacao' => 'boolean',
        'habilitacao_tecnica_exigida' => 'boolean',
        'qt_declaracao_ciencia' => 'boolean',
        'qt_registro_entidade' => 'boolean',
        'qt_indicacao_pessoal' => 'boolean',
        'qt_outro' => 'boolean',
        'qt_nao_exigida' => 'boolean',
        'criterio_sustentabilidade' => 'boolean',
        'riscos_assumidos_contratada' => 'boolean',
        // 8.x tipos
        'entrega_parcelas_quantidade' => 'integer',
        'entrega_primeira_em_dias' => 'integer',
        'entrega_aviso_antecedencia_dias' => 'integer',
        'validade_minima_entrega_dias' => 'integer',
        // 9.x tipos
        'prorrogacao_possivel' => 'boolean',
        'pagamento_prazo_dias' => 'integer',
        'garantia_contrato_percentual' => 'float',
    ];

    public function itens()
    {
        return $this->hasMany(TermoReferenciaItem::class, 'termo_referencia_id');
    }

    public function logs()
    {
        return $this->hasMany(TermoReferenciaLog::class, 'termo_referencia_id')
            ->orderBy('created_at', 'asc');
    }
}
