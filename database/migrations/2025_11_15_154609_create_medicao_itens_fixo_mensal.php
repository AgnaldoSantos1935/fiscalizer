<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicao_itens_fixo_mensal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicao_id')->constrained('medicoes')->onDelete('cascade');

            $table->string('descricao')->nullable(); // ex: "Serviços de suporte mensal"
            $table->boolean('servico_prestado')->default(true);
            $table->boolean('relatorio_entregue')->default(true);
            $table->boolean('chamados_atendidos')->default(true);
            $table->integer('chamados_pendentes')->default(0);

            $table->decimal('valor_mensal_contratado', 18, 2)->nullable();
            $table->decimal('valor_desconto', 18, 2)->nullable();
            $table->decimal('valor_final', 18, 2)->nullable();

            $table->json('observacoes_json')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicao_itens_fixo_mensal');
    }
};
