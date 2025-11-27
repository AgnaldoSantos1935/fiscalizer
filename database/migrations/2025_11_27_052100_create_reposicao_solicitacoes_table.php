<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reposicao_solicitacoes')) {
            Schema::create('reposicao_solicitacoes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('unidade_id')->constrained('unidades')->cascadeOnDelete();
                $table->foreignId('equipamento_id')->nullable()->constrained('equipamentos')->nullOnDelete();
                $table->foreignId('contrato_item_id')->constrained('contrato_itens')->cascadeOnDelete();
                $table->unsignedInteger('quantidade')->default(1);
                $table->string('status')->default('pendente');
                $table->text('motivo')->nullable();
                $table->unsignedBigInteger('solicitado_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reposicao_solicitacoes');
    }
};

