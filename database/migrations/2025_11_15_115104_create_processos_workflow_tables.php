<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🔹 Modelos de processos (genérico)
        if (!Schema::hasTable('processos')) {
            Schema::create('processos', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->string('codigo')->nullable(); // ex: PROJ_DEV_SIST
                $table->text('descricao')->nullable();
                $table->string('versao')->default('1.0');
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        // 🔹 Etapas do processo
        if (!Schema::hasTable('processo_etapas')) {
        Schema::create('processo_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->onDelete('cascade');
            $table->string('nome');
            $table->unsignedInteger('ordem')->default(1);
            $table->enum('tipo', ['inicio', 'execucao', 'aprovacao', 'validacao', 'fim'])->default('execucao');
            $table->unsignedInteger('prazo_horas')->nullable(); // SLA
            $table->string('responsavel_tipo')->nullable(); // ex: 'fiscal_tecnico', 'gestor', 'empresa', 'equipe_projeto'
            $table->json('checklist')->nullable(); // itens obrigatórios
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
        }

        // 🔹 Fluxos entre etapas (regras de transição)
        if (!Schema::hasTable('processo_fluxos')) {
        Schema::create('processo_fluxos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->onDelete('cascade');
            $table->foreignId('etapa_origem_id')->constrained('processo_etapas')->onDelete('cascade');
            $table->foreignId('etapa_destino_id')->constrained('processo_etapas')->onDelete('cascade');
            $table->json('regra')->nullable(); // ex: { "condicao": "valor_ust > 100" }
            $table->string('acao_automatica')->nullable(); // ex: 'notificar_gestor', 'gerar_documento'
            $table->timestamps();
        });
        }

        // 🔹 Instância de processo (para cada projeto)
        if (!Schema::hasTable('processo_instancias')) {
        Schema::create('processo_instancias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->onDelete('cascade');

            // morph para ligar a qualquer model (aqui usaremos Projeto)
            $table->morphs('referencia'); // referencia_type, referencia_id

            $table->enum('status', ['em_execucao', 'concluido', 'cancelado'])->default('em_execucao');
            $table->foreignId('iniciado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('data_inicio')->nullable();
            $table->timestamp('data_fim')->nullable();
            $table->timestamps();
        });
        }

        // 🔹 Etapas da instância (execução real)
        if (!Schema::hasTable('processo_instancia_etapas')) {
        Schema::create('processo_instancia_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instancia_id')->constrained('processo_instancias')->onDelete('cascade');
            $table->foreignId('etapa_id')->constrained('processo_etapas')->onDelete('cascade');

            $table->enum('status', ['pendente', 'em_execucao', 'concluida', 'atrasada'])->default('pendente');
            $table->timestamp('data_inicio')->nullable();
            $table->timestamp('data_fim')->nullable();

            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
        }

        // 🔹 Logs (trilha de auditoria)
        if (!Schema::hasTable('processo_logs')) {
        Schema::create('processo_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instancia_id')->constrained('processo_instancias')->onDelete('cascade');
            $table->foreignId('etapa_id')->nullable()->constrained('processo_etapas')->nullOnDelete();
            $table->string('acao'); // ex: 'avancar', 'retornar', 'criar', 'concluir'
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('mensagem')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_logs');
        Schema::dropIfExists('processo_instancia_etapas');
        Schema::dropIfExists('processo_instancias');
        Schema::dropIfExists('processo_fluxos');
        Schema::dropIfExists('processo_etapas');
        Schema::dropIfExists('processos');
    }
};
