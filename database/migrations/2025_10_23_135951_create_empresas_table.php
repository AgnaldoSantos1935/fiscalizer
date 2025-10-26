<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criação da tabela 'empresas'
     * Representa as empresas contratadas ou fornecedores de serviços.
     */
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();

            // Dados básicos da empresa
            $table->string('razao_social', 200);
            $table->string('nome_fantasia', 200)->nullable();
            $table->string('cnpj', 18)->unique(); // formato: 00.000.000/0000-00
            $table->string('inscricao_estadual', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 10)->nullable();

            // Auditoria
            $table->timestamps();        // created_at e updated_at
            $table->softDeletes();       // deleted_at para exclusão lógica
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Índices auxiliares
            $table->index('razao_social');
            $table->index('cnpj');
        });
    }

    /**
     * Reversão da tabela (rollback)
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
