<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criação da tabela 'contratos'
     * Representa contratos administrativos de serviços, obras e TIC.
     */
    public function up(): void
    {
        if (Schema::hasTable('contratos')) { return; }

        Schema::create('contratos', function (Blueprint $table) {
    $table->id();

    $table->string('numero')->unique();
    $table->string('processo_origem')->nullable();
    $table->string('modalidade')->nullable();

    $table->text('objeto');
    $table->text('objeto_resumido')->nullable();

    $table->decimal('valor_global', 18, 2)->nullable();
    $table->decimal('valor_mensal', 18, 2)->nullable();
    $table->integer('quantidade_meses')->nullable();

    $table->date('data_assinatura')->nullable();
    $table->date('data_inicio_vigencia')->nullable();
    $table->date('data_fim_vigencia')->nullable();

    // Empresa
    $table->string('empresa_razao_social')->nullable();
    $table->string('empresa_cnpj', 20)->nullable();
    $table->string('empresa_endereco')->nullable();
    $table->string('empresa_representante')->nullable();
    $table->string('empresa_contato')->nullable();
    $table->string('empresa_email')->nullable();

    // Fiscais
    $table->string('fiscal_tecnico')->nullable();
    $table->string('fiscal_administrativo')->nullable();
    $table->string('gestor')->nullable();

    // Riscos
    $table->integer('risco_score')->nullable();
    $table->string('risco_nivel', 20)->nullable();
    $table->json('risco_detalhes_json')->nullable();

    // JSONs
    $table->json('obrigacoes_contratada')->nullable();
    $table->json('obrigacoes_contratante')->nullable();
    $table->json('itens_fornecimento')->nullable();
    $table->json('anexos_detectados')->nullable();
    $table->json('clausulas')->nullable();
    $table->json('riscos_detectados')->nullable();

    $table->string('status')->default('Ativo');

    $table->timestamps();
});

    }

    /**
     * Reverte a criação da tabela (rollback)
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
