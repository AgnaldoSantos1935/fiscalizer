<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ordens_fornecimento')) {
            Schema::table('ordens_fornecimento', function (Blueprint $table) {
                // Cabeçalho
                if (! Schema::hasColumn('ordens_fornecimento', 'orgao_entidade')) {
                    $table->string('orgao_entidade')->nullable()->after('itens_json');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'unidade_requisitante')) {
                    $table->string('unidade_requisitante')->nullable()->after('orgao_entidade');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'cnpj_orgao')) {
                    $table->string('cnpj_orgao', 25)->nullable()->after('unidade_requisitante');
                }

                // Contrato/processo
                if (! Schema::hasColumn('ordens_fornecimento', 'contrato_numero')) {
                    $table->string('contrato_numero', 60)->nullable()->after('cnpj_orgao');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'processo_contratacao')) {
                    $table->string('processo_contratacao', 60)->nullable()->after('contrato_numero');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'modalidade')) {
                    $table->string('modalidade', 60)->nullable()->after('processo_contratacao');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'vigencia_inicio')) {
                    $table->date('vigencia_inicio')->nullable()->after('modalidade');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'vigencia_fim')) {
                    $table->date('vigencia_fim')->nullable()->after('vigencia_inicio');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'fundamentacao_legal')) {
                    $table->string('fundamentacao_legal')->nullable()->after('vigencia_fim');
                }

                // Contratada
                if (! Schema::hasColumn('ordens_fornecimento', 'contratada_razao_social')) {
                    $table->string('contratada_razao_social')->nullable()->after('fundamentacao_legal');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'contratada_cnpj')) {
                    $table->string('contratada_cnpj', 25)->nullable()->after('contratada_razao_social');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'contratada_endereco')) {
                    $table->string('contratada_endereco')->nullable()->after('contratada_cnpj');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'contratada_representante')) {
                    $table->string('contratada_representante')->nullable()->after('contratada_endereco');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'contratada_contato')) {
                    $table->string('contratada_contato')->nullable()->after('contratada_representante');
                }

                // Entrega e condições
                if (! Schema::hasColumn('ordens_fornecimento', 'prazo_entrega_dias')) {
                    $table->unsignedInteger('prazo_entrega_dias')->nullable()->after('contratada_contato');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'local_entrega')) {
                    $table->string('local_entrega')->nullable()->after('prazo_entrega_dias');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'horario_entrega')) {
                    $table->string('horario_entrega')->nullable()->after('local_entrega');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'recebimento_condicoes')) {
                    $table->text('recebimento_condicoes')->nullable()->after('horario_entrega');
                }

                // Obrigações e sanções
                if (! Schema::hasColumn('ordens_fornecimento', 'obrigacoes_contratada')) {
                    $table->text('obrigacoes_contratada')->nullable()->after('recebimento_condicoes');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'obrigacoes_administracao')) {
                    $table->text('obrigacoes_administracao')->nullable()->after('obrigacoes_contratada');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'sancoes')) {
                    $table->text('sancoes')->nullable()->after('obrigacoes_administracao');
                }

                // Assinaturas
                if (! Schema::hasColumn('ordens_fornecimento', 'autoridade_nome')) {
                    $table->string('autoridade_nome')->nullable()->after('sancoes');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'autoridade_cargo')) {
                    $table->string('autoridade_cargo')->nullable()->after('autoridade_nome');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'gestor_nome')) {
                    $table->string('gestor_nome')->nullable()->after('autoridade_cargo');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'gestor_portaria')) {
                    $table->string('gestor_portaria')->nullable()->after('gestor_nome');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'fiscal_nome')) {
                    $table->string('fiscal_nome')->nullable()->after('gestor_portaria');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'fiscal_portaria')) {
                    $table->string('fiscal_portaria')->nullable()->after('fiscal_nome');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'assinaturas_json')) {
                    $table->json('assinaturas_json')->nullable()->after('fiscal_portaria');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'assinatura_hash')) {
                    $table->string('assinatura_hash')->nullable()->after('assinaturas_json');
                }
                if (! Schema::hasColumn('ordens_fornecimento', 'verificacao_url')) {
                    $table->string('verificacao_url')->nullable()->after('assinatura_hash');
                }
            });
        }
    }

    public function down(): void
    {
        // Não remove colunas para evitar perda de dados
    }
};
