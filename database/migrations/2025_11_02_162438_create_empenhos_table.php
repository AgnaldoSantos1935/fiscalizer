<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('empenhos', function (Blueprint $table) {
            $table->id();

            // 🔹 Chave estrangeira para contrato
            $table->foreignId('contrato_id')
                  ->constrained('contratos')
                  ->onDelete('cascade'); // Exclui empenhos se o contrato for apagado

            // 🔹 Campos principais
            $table->string('numero', 50)->unique(); // Ex: 2025.160101NE005472
            $table->date('data_empenho')->nullable();
            $table->decimal('valor', 15, 2)->default(0);
            $table->text('descricao')->nullable();

            // 🔹 Campos administrativos
            $table->string('projeto_atividade', 20)->nullable(); // Ex: 8.904
            $table->string('fonte_recurso', 50)->nullable();
            $table->string('status', 20)->default('ativo'); // ativo, cancelado, etc.

            // 🔹 Auditoria
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empenhos');
    }
};
