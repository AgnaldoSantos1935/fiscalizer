<?php

namespace Database\Seeders;

use App\Models\TermoReferencia;
use App\Models\TermoReferenciaItem;
use App\Models\TermoReferenciaLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TermoReferenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Garantir um usuário para vincular nos logs
            $user = User::first();
            if (! $user) {
                $user = User::create([
                    'name' => 'Admin TR',
                    'email' => 'admin.tr@example.com',
                    'password' => Hash::make('password'),
                ]);
            }

            // Helper para criar TR com itens e logs
            $this->seedTRComItensELogs(
                titulo: 'Aquisição de Equipamentos de TI',
                status: 'rascunho',
                itens: [
                    ['descricao' => 'Notebook 14"', 'unidade' => 'un', 'quantidade' => 10, 'valor_unitario' => 3500.00],
                    ['descricao' => 'Monitor 24"', 'unidade' => 'un', 'quantidade' => 15, 'valor_unitario' => 800.00],
                ],
                userId: $user->id,
                logsAcoes: []
            );

            $this->seedTRComItensELogs(
                titulo: 'Serviço de Manutenção de Rede',
                status: 'em_analise',
                itens: [
                    ['descricao' => 'Manutenção mensal', 'unidade' => 'mês', 'quantidade' => 12, 'valor_unitario' => 2500.00],
                    ['descricao' => 'Atendimento emergencial', 'unidade' => 'chamado', 'quantidade' => 20, 'valor_unitario' => 150.00],
                ],
                userId: $user->id,
                logsAcoes: [
                    ['acao' => 'enviar_aprovacao', 'motivo' => 'TR finalizada para análise da coordenação.'],
                ]
            );

            $this->seedTRComItensELogs(
                titulo: 'Compra de Material Didático',
                status: 'finalizado',
                itens: [
                    ['descricao' => 'Kits de livros', 'unidade' => 'kit', 'quantidade' => 50, 'valor_unitario' => 120.00],
                    ['descricao' => 'Material de apoio', 'unidade' => 'un', 'quantidade' => 100, 'valor_unitario' => 35.50],
                ],
                userId: $user->id,
                logsAcoes: [
                    ['acao' => 'enviar_aprovacao', 'motivo' => 'Encaminhado para aprovação do setor.'],
                    ['acao' => 'aprovar', 'motivo' => 'Conteúdo consistente e orçamento adequado.'],
                ]
            );
        });
    }

    private function seedTRComItensELogs(string $titulo, string $status, array $itens, int $userId, array $logsAcoes = []): void
    {
        // Criar TR com cabeçalho mínimo e campos complementares
        $tr = TermoReferencia::create([
            'titulo' => $titulo,
            'tipo_tr' => 'Aquisição',
            'pae_numero' => 'PAE-' . rand(1000, 9999),
            'cidade' => 'São Paulo',
            'data_emissao' => now()->toDateString(),
            'responsavel_nome' => 'Fulano de Tal',
            'responsavel_cargo' => 'Analista de Compras',
            'responsavel_matricula' => '123456',
            'objeto' => 'Aquisição/serviço conforme itens especificados.',
            'justificativa' => 'Atender demanda operacional.',
            'escopo' => 'Conforme especificações técnicas anexas.',
            'requisitos' => 'Atender normas e padrões internos.',
            'criterios_julgamento' => 'Menor preço ou melhor técnica conforme edital.',
            'prazos' => 'Prazo de entrega conforme edital.',
            'local_execucao' => 'Unidades da rede municipal.',
            'forma_pagamento' => '30 dias após recebimento.',
            'modelo_execucao' => 'Execução direta.',
            'modelo_gestao' => 'Gestão pela coordenação de TI.',
            'criterios_medicao_pagamento' => 'Mensalidade e entregáveis validados.',
            'forma_criterios_selecao_fornecedor' => 'Critérios objetivos definidos em edital.',
            'especificacao_produto' => 'Especificações detalhadas conforme anexo técnico.',
            'locais_entrega_recebimento' => 'Diversas unidades conforme cronograma.',
            'garantia_manutencao_assistencia' => 'Garantia de 12 meses e assistência incluída.',
            'estimativas_valor_texto' => 'Estimativa baseada em histórico e pesquisa de mercado.',
            'adequacao_orcamentaria' => 'Compatível com dotação orçamentária vigente.',
            // Flags e campos complementares frequentes
            'garantia_exigida' => true,
            'manutencao_incluida' => false,
            'assistencia_tecnica_incluida' => true,
            'adequacao_orcamentaria_confirmada' => true,
            'prova_qualidade' => false,
            'edital_exigira_amostra' => false,
            'garantia_bem' => true,
            'garantia_bem_itens' => 'Peças e componentes críticos',
            'garantia_bem_meses' => 12,
            'assistencia_tecnica_tipo' => 'Local',
            'assistencia_tecnica_meses' => 12,
            'forma_contratacao' => 'Pregão Eletrônico',
            'criterio_julgamento_tipo' => 'Menor preço',
            'orcamento_sigiloso' => false,
            'itens_exclusivos_me_epp' => false,
            'habilitacao_juridica_existencia' => true,
            'habilitacao_juridica_autorizacao' => true,
            'habilitacao_tecnica_exigida' => true,
            'qt_declaracao_ciencia' => true,
            'qt_registro_entidade' => false,
            'qt_indicacao_pessoal' => false,
            'qt_outro' => false,
            'qt_nao_exigida' => false,
            'criterio_sustentabilidade' => false,
            'riscos_assumidos_contratada' => false,
            'entrega_forma' => 'Parcelada',
            'entrega_parcelas_quantidade' => 2,
            'entrega_primeira_em_dias' => 30,
            'entrega_aviso_antecedencia_dias' => 5,
            'recebimento_endereco' => 'Av. Central, 1000',
            'recebimento_horario' => '9h às 17h',
            'validade_minima_entrega_dias' => 90,
            'prazo_contrato' => '12 meses',
            'prorrogacao_possivel' => true,
            'pagamento_meio' => 'Transferência bancária',
            'pagamento_onde' => 'Tesouraria',
            'pagamento_prazo_dias' => 30,
            'regularidade_fiscal_prova_tipo' => 'Certidões negativas',
            'garantia_contrato_tipo' => 'Seguro garantia',
            'garantia_contrato_percentual' => 5.0,
            'garantia_contrato_justificativa' => 'Mitigar risco de inadimplemento.',
            'funcional_programatica' => '12.345.6789.0',
            'elemento_despesa' => '33.90.39',
            'fonte_recurso' => 'Recursos próprios',
            'status' => $status,
        ]);

        // Adicionar itens e calcular o valor estimado
        $valorTotal = 0;
        foreach ($itens as $it) {
            $item = new TermoReferenciaItem([
                'descricao' => $it['descricao'],
                'unidade' => $it['unidade'] ?? 'un',
                'quantidade' => $it['quantidade'],
                'valor_unitario' => $it['valor_unitario'],
            ]);
            $tr->itens()->save($item);
            $valorTotal += (float) $item->valor_total;
        }

        $tr->update(['valor_estimado' => $valorTotal]);

        // Criar logs de workflow conforme solicitado
        foreach ($logsAcoes as $log) {
            TermoReferenciaLog::create([
                'termo_referencia_id' => $tr->id,
                'acao' => $log['acao'],
                'usuario_id' => $userId,
                'motivo' => $log['motivo'] ?? null,
            ]);
        }
    }
}
