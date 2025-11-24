<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ordens_fornecimento')) {
            Schema::create('ordens_fornecimento', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
                $table->foreignId('empenho_id')->constrained('empenhos')->cascadeOnDelete();

                $table->string('numero_of', 30);
                $table->unsignedSmallInteger('ano_of');
                $table->dateTime('data_emissao')->nullable();

                $table->string('arquivo_pdf')->nullable();
                $table->json('itens_json');

                // Campos do cabeçalho conforme o modelo
                $table->string('orgao_entidade')->nullable();
                $table->string('unidade_requisitante')->nullable();
                $table->string('cnpj_orgao', 25)->nullable();

                // Dados do contrato/processo
                $table->string('contrato_numero', 60)->nullable();
                $table->string('processo_contratacao', 60)->nullable();
                $table->string('modalidade', 60)->nullable();
                $table->date('vigencia_inicio')->nullable();
                $table->date('vigencia_fim')->nullable();
                $table->string('fundamentacao_legal')->nullable();

                // Dados da contratada
                $table->string('contratada_razao_social')->nullable();
                $table->string('contratada_cnpj', 25)->nullable();
                $table->string('contratada_endereco')->nullable();
                $table->string('contratada_representante')->nullable();
                $table->string('contratada_contato')->nullable(); // telefone/email

                // Entrega e condições
                $table->unsignedInteger('prazo_entrega_dias')->nullable();
                $table->string('local_entrega')->nullable();
                $table->string('horario_entrega')->nullable();
                $table->text('recebimento_condicoes')->nullable();

                // Obrigações e sanções
                $table->text('obrigacoes_contratada')->nullable();
                $table->text('obrigacoes_administracao')->nullable();
                $table->text('sancoes')->nullable();

                // Assinaturas/autorização
                $table->string('autoridade_nome')->nullable();
                $table->string('autoridade_cargo')->nullable();
                $table->string('gestor_nome')->nullable();
                $table->string('gestor_portaria')->nullable();
                $table->string('fiscal_nome')->nullable();
                $table->string('fiscal_portaria')->nullable();
                $table->json('assinaturas_json')->nullable();
                $table->string('assinatura_hash')->nullable();
                $table->string('verificacao_url')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ordens_fornecimento');
    }
};
