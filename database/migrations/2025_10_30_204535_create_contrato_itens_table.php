<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_itens', function (Blueprint $table) {
            $table->id();

            // 🔗 Relacionamento principal
            $table->unsignedBigInteger('contrato_id');

            // 📋 Dados do item contratado
            $table->string('descricao_item', 255);
            $table->string('unidade_medida', 50)->nullable();
            $table->decimal('quantidade', 10, 2)->default(0);
            $table->decimal('valor_unitario', 14, 2)->default(0);
            $table->decimal('valor_total', 14, 2)->default(0);

            // 📊 Classificação
            $table->enum('tipo_item', ['servico', 'material', 'software', 'outros'])->default('servico');
            $table->enum('status', ['ativo', 'executado', 'cancelado'])->default('ativo');

            // 🕓 Controle de histórico
            $table->timestamps();
            $table->softDeletes();

            // 🧾 Auditoria
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // 🔑 Foreign key
            $table->foreign('contrato_id')
                  ->references('id')
                  ->on('contratos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_itens');
    }
};
