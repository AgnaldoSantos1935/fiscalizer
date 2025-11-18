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
        if (Schema::hasTable('medicoes')) {
            return;
        }

        Schema::create('medicoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            $table->string('competencia', 7); // MM/AAAA
            $table->enum('tipo', ['software', 'telco', 'fixo_mensal']);

            $table->decimal('valor_bruto', 18, 2)->nullable();
            $table->decimal('valor_desconto', 18, 2)->nullable();
            $table->decimal('valor_liquido', 18, 2)->nullable();

            $table->decimal('sla_alcancado', 5, 2)->nullable(); // telco
            $table->decimal('sla_contratado', 5, 2)->nullable(); // telco

            $table->string('status')->default('rascunho');
            // rascunho, enviado_fiscal_tecnico, aprovado, reprovado, enviado_pagamento

            $table->json('resumo_json')->nullable(); // qualquer agregado/calculo
            $table->json('inconsistencias_json')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicoes');
    }
};
