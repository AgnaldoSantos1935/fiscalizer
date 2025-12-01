<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('equipamento_ocorrencias')) {
            Schema::create('equipamento_ocorrencias', function (Blueprint $table) {
                $table->id();
                $table->foreignId('equipamento_id')->constrained('equipamentos')->onDelete('cascade');
                $table->string('tipo');
                $table->text('descricao')->nullable();
                $table->json('fotos')->nullable();
                $table->string('status')->default('aberta');
                $table->unsignedBigInteger('reportado_by')->nullable();
                $table->unsignedBigInteger('recebida_por')->nullable();
                $table->unsignedBigInteger('avaliada_por')->nullable();
                $table->string('analise_status')->nullable();
                $table->text('analise_observacoes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('equipamento_ocorrencias');
    }
};
