<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('requisitos')) {
            Schema::create('requisitos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('demanda_id');
                $table->string('codigo_interno')->nullable();
                $table->string('titulo');
                $table->text('descricao')->nullable();
                $table->string('etapa')->nullable();
                $table->string('tipo')->default('evolutivo');
                $table->string('complexidade')->default('media');
                $table->timestamps();

                $table->foreign('demanda_id')
                    ->references('id')
                    ->on('demandas')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Não faz nada para evitar remover dados acidentalmente
    }
};
