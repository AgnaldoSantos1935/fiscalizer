<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscalizacoes_projetos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('projeto_id');
            $table->unsignedBigInteger('apf_id')->nullable(); // fiscalização pode ser por projeto ou por APF específica
            $table->enum('tipo_fiscalizacao', ['Tecnica', 'Administrativa']);
            $table->date('data_verificacao');
            $table->string('fiscal_responsavel');
            $table->text('descricao_verificacao')->nullable();
            $table->enum('status', ['Conforme', 'Nao Conforme', 'Pendente'])->default('Pendente');
            $table->enum('nivel_risco', ['Baixo', 'Medio', 'Alto'])->default('Baixo');
            $table->json('evidencias')->nullable(); // caminhos/links de arquivos
            $table->text('recomendacoes')->nullable();
            $table->timestamps();
            $table->foreign('projeto_id')->references('id')->on('projetos_software')->cascadeOnDelete();
            $table->foreign('apf_id')->references('id')->on('apfs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscalizacoes_projetos');
    }
};
