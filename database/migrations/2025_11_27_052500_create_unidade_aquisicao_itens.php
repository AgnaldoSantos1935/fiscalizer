<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('unidade_aquisicao_itens')) {
            Schema::create('unidade_aquisicao_itens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('unidade_id')->constrained('unidades')->cascadeOnDelete();
                $table->string('tipo')->nullable();
                $table->string('descricao');
                $table->json('especificacoes')->nullable();
                $table->string('documento')->nullable();
                $table->decimal('valor', 18, 2)->nullable();
                $table->date('data_aquisicao')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('unidade_aquisicao_itens');
    }
};

