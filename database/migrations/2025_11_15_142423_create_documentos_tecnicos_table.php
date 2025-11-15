<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentos_tecnicos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('demanda_id');
            $table->string('arquivo_path');    // storage path
            $table->string('arquivo_original')->nullable();

            // Campos extraídos / consolidados
            $table->integer('pf_estimado')->nullable();
            $table->integer('ust_estimado')->nullable();
            $table->json('requisitos_json')->nullable();    // lista de requisitos extraídos
            $table->json('cronograma_json')->nullable();    // datas/marcos
            $table->json('telas_json')->nullable();         // protótipos/telas identificadas

            // IA / validação
            $table->string('status_validacao')->default('pendente'); // pendente, valido, invalido
            $table->json('inconsistencias_json')->nullable();        // array de strings
            $table->json('resumo_ia_json')->nullable();              // resumo estruturado

            $table->timestamps();

            $table->foreign('demanda_id')->references('demanda_id')->on('demandas')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_tecnicos');
    }
};
