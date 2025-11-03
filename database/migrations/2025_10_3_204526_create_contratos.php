<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criação da tabela 'contratos'
     * Representa contratos administrativos de serviços, obras e TIC.
     */
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();

            // 📄 Dados principais
            $table->string('numero', 30)->unique(); // Ex: 065/2025
            $table->text('objeto'); // Descrição do objeto do contrato

            // 🏢 Empresa contratada
            $table->foreignId('contratada_id')
                ->constrained('empresas')
                ->onDelete('cascade');

            // 👥 Fiscais e gestor
            $table->foreignId('fiscal_tecnico_id')
                ->nullable()
                ->constrained('pessoas')
                ->nullOnDelete();

            $table->foreignId('fiscal_administrativo_id')
                ->nullable()
                ->constrained('pessoas')
                ->nullOnDelete();

            $table->foreignId('gestor_id')
                ->nullable()
                ->constrained('pessoas')
                ->nullOnDelete();

            // 💰 Valores e datas
            $table->decimal('valor_global', 14, 2)->default(0);
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();

            // ⚙️ Situação e classificação
            $table->enum('situacao', ['vigente', 'encerrado', 'rescindido', 'suspenso'])
                ->default('vigente');
            $table->enum('tipo', ['TI', 'Serviço', 'Obra', 'Material'])
                ->default('TI');

            // 🔗 Situação detalhada (chave estrangeira)
            $table->foreignId('situacao_id')
                ->nullable()
                ->constrained('situacoes')
                ->nullOnDelete();

            // 🧾 Auditoria
            $table->timestamps();        // created_at e updated_at
            $table->softDeletes();       // exclusão lógica
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 🔍 Índices auxiliares
            $table->index('numero');
            $table->index('situacao');
            $table->index('tipo');
            $table->index('situacao_id');
        });
    }

    /**
     * Reverte a criação da tabela (rollback)
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
