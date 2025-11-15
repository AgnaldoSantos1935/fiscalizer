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
        // Evita erro quando a tabela já existe em ambientes com base pré-carregada
        if (Schema::hasTable('situacoes_contratos')) {
            return;
        }

        Schema::create('situacoes_contratos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50); // Ex: Vigente, Encerrado...
            $table->string('slug', 30)->unique(); // Ex: vigente, encerrado, rescindido
            $table->string('descricao', 30)->unique(); // Ex: Descrição do status
            $table->string('cor', 20)->nullable(); // opcional: cor pra exibir badge
            $table->timestamps();
        });
    }
public function down(): void
{
    Schema::dropIfExists('situacoes_contratos');
}
};
