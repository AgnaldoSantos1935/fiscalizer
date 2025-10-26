<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criação da tabela 'projetos'
     * Cada projeto ou serviço vinculado a um contrato.
     */
    public function up(): void
    {
        Schema::create('projetos', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->foreignId('contrato_id')
                ->constrained('contratos')
                ->onDelete('cascade');

            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->enum('status', ['planejado', 'em_execucao', 'concluido', 'suspenso', 'cancelado'])
                ->default('planejado');

            // Controle de datas
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();

            // Auditoria
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Índices auxiliares
            $table->index(['contrato_id', 'status']);
        });
    }

    /**
     * Reversão da tabela.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
