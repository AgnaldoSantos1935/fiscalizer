<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('demandas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('projeto_id')->nullable();
            $table->unsignedBigInteger('sistema_id')->nullable();
            $table->unsignedBigInteger('modulo_id')->nullable();

            $table->string('tipo_manutencao')
                ->comment('corretiva, evolutiva, adaptativa, perfectiva');

            $table->string('titulo');
            $table->text('descricao')->nullable();

            $table->date('data_abertura')->nullable();
            $table->date('data_fechamento')->nullable();

            $table->string('prioridade')->default('media'); // baixa, media, alta, crítica
            $table->string('status')->default('aberta');    // aberta, em andamento, concluída, cancelada

            $table->timestamps();

            // FK opcionais
            $table->foreign('projeto_id')->references('id')->on('projetos')->nullOnDelete();
            $table->foreign('sistema_id')->references('id')->on('sistemas')->nullOnDelete();
            $table->foreign('modulo_id')->references('id')->on('modulos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandas');
    }
};
