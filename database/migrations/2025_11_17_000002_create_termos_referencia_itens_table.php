<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('termos_referencia_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termo_referencia_id')
                  ->constrained('termos_referencia')
                  ->onDelete('cascade');
            $table->string('descricao');
            $table->string('unidade')->nullable();
            $table->decimal('quantidade', 15, 2)->default(0);
            $table->decimal('valor_unitario', 15, 2)->default(0);
            $table->decimal('valor_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termos_referencia_itens');
    }
};