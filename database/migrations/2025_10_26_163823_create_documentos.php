<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criação da tabela 'documentos'
     * Registra todos os documentos associados a contratos (TR, ETP, Relatórios etc.)
     */
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();

            // Relacionamento com contrato
            $table->foreignId('contrato_id')
                ->constrained('contratos')
                ->onDelete('cascade');

            // Tipo de documento
            $table->enum('tipo', [
                'TR',            // Termo de Referência
                'ETP',           // Estudo Técnico Preliminar
                'PARECER',       // Parecer Técnico ou Jurídico
                'NOTA_TECNICA',  // Nota Técnica
                'RELATORIO',     // Relatório
                'OUTROS'         // Qualquer outro documento
            ])->default('OUTROS');

            // Identificação e controle
            $table->string('titulo', 200)->nullable();
            $table->string('descricao', 500)->nullable();
            $table->string('caminho_arquivo', 255)->nullable(); // caminho ou nome no storage
            $table->string('versao', 20)->nullable(); // ex: v1.0, v2.1
            $table->date('data_upload')->nullable();

            // Auditoria
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Índices auxiliares
            $table->index(['contrato_id', 'tipo']);
            $table->index('data_upload');
        });
    }

    /**
     * Reversão da criação da tabela.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
