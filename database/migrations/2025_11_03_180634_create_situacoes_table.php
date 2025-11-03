<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criação da tabela 'situacoes'
     * Utilizada como catálogo de status administrativos e contratuais.
     */
    public function up(): void
    {
        Schema::create('situacoes', function (Blueprint $table) {
            $table->id();

            // Nome da situação
            $table->string('nome', 100); // Ex: Vigente, Encerrado, Suspenso, Rescindido

            // Descrição detalhada
            $table->string('descricao', 255)->nullable();

            // Cor padrão (para exibição em badges no sistema)
            $table->string('cor', 20)->nullable(); // Ex: 'success', 'warning', 'danger'

            // Indicador de uso
            $table->boolean('ativo')->default(true);

            // Auditoria
            $table->timestamps();
        });
    }

    /**
     * Reverte a criação da tabela (rollback)
     */
    public function down(): void
    {
        Schema::dropIfExists('situacoes');
    }
};
