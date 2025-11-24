<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('medicao_itens')) {
            return; // evita criação duplicada
        }

        Schema::create('medicao_itens', function (Blueprint $table) {
            $table->id();

            // Relacionamento
            $table->foreignId('medicao_id')
                ->constrained('medicoes')
                ->onDelete('cascade');

            $table->foreignId('projeto_id')
                ->nullable()
                ->constrained('projetos')
                ->nullOnDelete();

            // Identificação do serviço ou projeto
            $table->string('descricao', 255); // Ex: “Desenvolvimento módulo de autenticação”

            // Pontos e métricas
            $table->decimal('pontos_funcao', 10, 2)->default(0);
            $table->decimal('ust', 10, 2)->default(0);

            // Valores
            $table->decimal('valor_unitario_pf', 10, 2)->default(0);
            $table->decimal('valor_unitario_ust', 10, 2)->default(0);
            $table->decimal('valor_total', 14, 2)->default(0);

            // Auditoria e controle
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->index(['medicao_id', 'projeto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicao_itens');
    }
};
