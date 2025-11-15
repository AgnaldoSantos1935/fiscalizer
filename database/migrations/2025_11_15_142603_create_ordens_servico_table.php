<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ordens_servico', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('demanda_id');
            $table->unsignedBigInteger('documento_tecnico_id');

            $table->string('numero_os')->unique();
            $table->year('ano_os');

            $table->integer('pf_total')->nullable();
            $table->integer('ust_total')->nullable();

            $table->json('cronograma_json')->nullable();
            $table->json('requisitos_json')->nullable();

            $table->date('data_emissao');
            $table->string('arquivo_pdf'); // path no storage

            $table->string('status')->default('emitida'); // emitida, cancelada, concluida...

            $table->timestamps();

            $table->foreign('demanda_id')->references('id')->on('demandas')->cascadeOnDelete();
            $table->foreign('documento_tecnico_id')->references('id')->on('documentos_tecnicos')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordens_servico');
    }
};
