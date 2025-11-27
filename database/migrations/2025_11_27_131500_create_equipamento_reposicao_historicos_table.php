<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipamento_reposicao_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')->constrained('unidades')->cascadeOnDelete();
            $table->foreignId('equipamento_id')->nullable()->constrained('equipamentos')->nullOnDelete();
            $table->foreignId('reposicao_id')->nullable()->constrained('reposicao_solicitacoes')->nullOnDelete();
            $table->foreignId('novo_equipamento_id')->nullable()->constrained('equipamentos')->nullOnDelete();
            $table->string('evento'); // solicitada, aprovada, entregue, baixa
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->index(['unidade_id','equipamento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipamento_reposicao_historicos');
    }
};

