<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_empenho_itens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('nota_empenho_id')->constrained('notas_empenho')->onDelete('cascade');
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
