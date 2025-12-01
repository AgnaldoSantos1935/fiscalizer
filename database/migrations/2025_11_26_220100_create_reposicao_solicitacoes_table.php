<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Evita erro: FK para 'unidades' antes da tabela existir.
        if (Schema::hasTable('reposicao_solicitacoes')) {
            return;
        }

        if (! Schema::hasTable('unidades')) {
            return;
        }

        Schema::create('reposicao_solicitacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')->constrained('unidades')->onDelete('cascade');
            $table->foreignId('equipamento_id')->nullable()->constrained('equipamentos')->nullOnDelete();
            $table->foreignId('contrato_item_id')->constrained('contrato_itens')->onDelete('cascade');
            $table->integer('quantidade');
            $table->string('status')->default('pendente');
            $table->text('motivo')->nullable();
            $table->string('cit_decisao')->nullable();
            $table->text('cit_observacoes')->nullable();
            $table->unsignedBigInteger('cit_usuario_id')->nullable();
            $table->unsignedBigInteger('detec_usuario_id')->nullable();
            $table->timestamp('aprovada_em')->nullable();
            $table->timestamp('entregue_em')->nullable();
            $table->timestamp('baixado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reposicao_solicitacoes');
    }
};
