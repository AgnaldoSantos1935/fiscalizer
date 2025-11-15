<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requisitos_sistema', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('demanda_id');
            $table->string('codigo_interno')->nullable();

            $table->string('titulo');
            $table->text('descricao')->nullable();

            $table->string('etapa')->nullable()
                ->comment('levantamento, analise, desenvolvimento, testes');

            $table->string('tipo')->default('evolutivo')
                ->comment('novo, alteracao, correção, evolutivo, adaptativo');

            $table->string('complexidade')->default('media')
                ->comment('baixa, media, alta');

            $table->timestamps();

            $table->foreign('demanda_id')->references('id')->on('demandas')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitos');
    }
};
