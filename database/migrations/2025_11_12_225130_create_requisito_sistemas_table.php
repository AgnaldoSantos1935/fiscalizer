<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cronogramas_projeto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            $table->string('etapa');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('pessoas')->nullOnDelete();
            $table->enum('status', ['planejado', 'em_execucao', 'concluido', 'atrasado'])->default('planejado');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cronogramas_projeto');
    }
};
