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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // ex: contratos_view, medicoes_validar, financeiro_pagamentos_autorizar
            $table->string('nome'); // ex: Visualizar Contratos
            $table->string('descricao')->nullable();
            $table->string('modulo')->nullable(); // ex: contratos, medicoes, financeiro
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
