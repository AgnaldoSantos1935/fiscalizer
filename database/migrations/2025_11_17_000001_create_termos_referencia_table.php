<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('termos_referencia', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('objeto')->nullable();
            $table->text('justificativa')->nullable();
            $table->text('escopo')->nullable();
            $table->text('requisitos')->nullable();
            $table->text('criterios_julgamento')->nullable();
            $table->text('prazos')->nullable();
            $table->text('local_execucao')->nullable();
            $table->text('forma_pagamento')->nullable();
            $table->decimal('valor_estimado', 15, 2)->nullable();
            $table->string('status')->default('rascunho');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termos_referencia');
    }
};