<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('normas_trechos')) {
            Schema::create('normas_trechos', function (Blueprint $table) {
                $table->id();
                $table->string('fonte');
                $table->string('referencia')->nullable();
                $table->string('idioma')->default('pt-BR');
                $table->string('arquivo_pdf')->nullable();
                $table->integer('trecho_ordem')->nullable();
                $table->text('trecho_texto');
                $table->json('tags')->nullable();
                $table->json('embedding')->nullable();
                $table->timestamps();
                $table->index(['fonte', 'referencia']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('normas_trechos');
    }
};
