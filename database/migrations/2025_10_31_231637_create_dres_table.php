<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Evita erro quando a tabela já existe em ambientes com base pré-carregada
        if (Schema::hasTable('dres')) {
            return;
        }

        Schema::create('dres', function (Blueprint $table) {
            $table->id();
            $table->string('codigodre', 10)->unique();
            $table->string('nome_dre', 150);
            $table->string('municipio_sede', 100);
            $table->string('email', 150)->nullable();
            $table->string('telefone', 50)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dres');
    }
};
