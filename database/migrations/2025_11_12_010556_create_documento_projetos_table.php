<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_projetos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscalizacao_id');
            $table->string('tipo')->nullable(); // Ata, Relatório, Evidência, Prototipo, Print
            $table->string('arquivo'); // storage path
            $table->string('titulo')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
            $table->foreign('fiscalizacao_id')->references('id')->on('fiscalizacoes_projetos')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_projetos');
    }
};
