<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Evita duplicidade caso a tabela já exista (há outra migração semelhante)
        if (Schema::hasTable('notas_empenho_itens')) {
            return;
        }

        Schema::create('notas_empenho_itens', function (Blueprint $table) {
            $table->id();

            // Corrige FK: itens pertencem à tabela 'empenhos'
            $table->foreignId('nota_empenho_id')->constrained('empenhos')->onDelete('cascade');
            $table->integer('item_numero')->nullable();
            $table->string('descricao', 255);
            $table->string('unidade', 50)->nullable();
            $table->decimal('quantidade', 12, 2)->default(1);
            $table->decimal('valor_unitario', 14, 2)->default(0);
            $table->decimal('valor_total', 14, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_empenho_itens');
    }
};
