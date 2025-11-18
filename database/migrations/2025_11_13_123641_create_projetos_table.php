<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Evita erro quando a tabela já existe em ambientes com base pré-carregada
        if (Schema::hasTable('projetos')) {
            return;
        }

        Schema::create('projetos', function (Blueprint $table) {
            $table->id();

            // Código identificador interno do projeto
            $table->string('codigo')->unique()->nullable();

            // Título curto do projeto
            $table->string('titulo');

            // Descrição longa
            $table->text('descricao')->nullable();

            // Sistema/módulo afetado
            $table->string('sistema')->nullable();
            $table->string('modulo')->nullable();

            // Contrato vinculado
            $table->foreignId('contrato_id')->nullable()->constrained('contratos')->nullOnDelete();

            // Item do contrato
            $table->foreignId('itemcontrato_id')->nullable()->constrained('contrato_itens')->nullOnDelete();

            // Gerentes
            $table->foreignId('gerente_tecnico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gerente_adm_id')->nullable()->constrained('users')->nullOnDelete();

            // DRE e escola (se houver)
            $table->foreignId('dre_id')->nullable()->constrained('dres')->nullOnDelete();
            $table->foreignId('escola_id')->nullable()->constrained('escolas')->nullOnDelete();

            // Datas
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();

            // Situação
            $table->enum('situacao', [
                'analise',
                'planejado',
                'em_execucao',
                'homologacao',
                'aguardando_pagamento',
                'concluido',
                'suspenso',
                'cancelado',
            ])->default('planejado');

            // PF e UST
            $table->decimal('pf_planejado', 10, 2)->default(0);
            $table->decimal('pf_entregue', 10, 2)->default(0);

            $table->decimal('ust_planejada', 10, 2)->default(0);
            $table->decimal('ust_entregue', 10, 2)->default(0);

            // Esforço em horas
            $table->integer('horas_planejadas')->default(0);
            $table->integer('horas_registradas')->default(0);

            // Priorização / criticidade
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'critica'])->default('media');

            // Status geral calculado automaticamente
            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
