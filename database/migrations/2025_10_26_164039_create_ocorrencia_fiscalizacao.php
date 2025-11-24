<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criação da tabela 'ocorrencias_fiscalizacao'
     * Registra notificações, glosas, advertências e não conformidades nos contratos.
     */
    public function up(): void
    {
        if (Schema::hasTable('ocorrencias_fiscalizacao')) {
            return; // evita criação duplicada
        }

        Schema::create('ocorrencias_fiscalizacao', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();

            // Relacionamento com o contrato fiscalizado
            $table->foreignId('contrato_id')
                ->constrained('contratos')
                ->onDelete('cascade');

            // Responsável pelo registro (servidor ou fiscal)
            $table->foreignId('responsavel_id')
                ->nullable()
                ->constrained('pessoas')
                ->nullOnDelete();

            // Dados principais da ocorrência
            $table->date('data_ocorrencia')->nullable();
            $table->enum('tipo', ['advertencia', 'glosa', 'nao_conformidade', 'outros'])
                ->default('outros');
            $table->enum('gravidade', ['baixa', 'media', 'alta'])->default('baixa');
            $table->text('descricao')->nullable();
            $table->text('providencias')->nullable();

            // Auditoria e controle
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Índices auxiliares
            $table->index(['contrato_id', 'tipo']);
            $table->index('data_ocorrencia');
        });
    }

    /**
     * Reversão da tabela.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocorrencias_fiscalizacao');
    }
};
