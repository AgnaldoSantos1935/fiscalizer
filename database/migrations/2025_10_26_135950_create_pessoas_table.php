<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criação da tabela 'pessoas'
     * Representa servidores públicos (fiscais, gestores, etc.)
     */
    public function up(): void
    {
        Schema::create('pessoas', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();

            // Dados pessoais e funcionais
            $table->string('nome');
            $table->string('matricula')->nullable();
            $table->string('cpf', 14)->unique()->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('cargo')->nullable();
            $table->enum('tipo_vinculo', ['efetivo', 'comissionado', 'terceirizado'])->default('efetivo');

            // Auditoria
            $table->timestamps(); // created_at e updated_at
            $table->softDeletes(); // deleted_at
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reversão da tabela (rollback)
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
