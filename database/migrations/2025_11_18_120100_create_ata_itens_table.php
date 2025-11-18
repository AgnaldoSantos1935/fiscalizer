<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ata_itens', function (Blueprint $t) {
            $t->id();
            $t->foreignId('ata_id')->constrained('atas_registro_precos')->onDelete('cascade');
            $t->string('descricao');
            $t->string('unidade')->nullable();
            $t->decimal('quantidade', 18, 2)->default(0);
            $t->decimal('preco_unitario', 18, 4)->default(0);
            $t->string('lote')->nullable();
            $t->string('grupo')->nullable();
            $t->string('marca')->nullable();
            $t->string('referencia')->nullable();
            $t->decimal('saldo_disponivel', 18, 2)->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ata_itens');
    }
};
