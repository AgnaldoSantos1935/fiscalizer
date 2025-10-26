<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criação da tabela 'medicoes'
     * Registra medições de Pontos de Função (PF) e valores por contrato.
     */
    public function up(): void
    {
        Schema::create('medicoes', function (Blueprint $table) {
            $table->id();

            // Relação com o contrato
            $table->foreignId('contrato_id')
                ->constrained('contratos')
                ->onDelete('cascade');

            // Período de referência (Ex: 2025-10)
            $table->string('mes_referencia', 7); // formato AAAA-MM
            $table->decimal('total_pf', 10, 2)->default(0);
            $table->decimal('valor_unitario_pf', 10, 2)->default(0);
            $table->decimal('valor_total', 14, 2)->default(0);

            // Controle de envio e situação
            $table->date('data_envio')->nullable();
            $table->enum('status', ['pendente', 'em_analise', 'aprovado', 'reprovado'])
                ->default('pendente');
            $table->text('observacao')->nullable();

            // Auditoria
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // exclusão lógica
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Índices auxiliares
            $table->index(['contrato_id', 'mes_referencia']);
            $table->index('status');
        });
    }

    /**
     * Reversão da criação da tabela.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicoes');
    }
};
