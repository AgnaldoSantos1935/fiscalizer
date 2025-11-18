<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Evita erro quando a tabela já existe em ambientes com base pré-carregada
        if (Schema::hasTable('pessoas')) {
            return;
        }

        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();

            // Pessoa também é usuário do sistema (opcional no ato do cadastro)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('nome_completo');
            $table->string('cpf', 14)->unique();
            $table->string('rg', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->enum('sexo', ['M', 'F', 'Outro'])->nullable();

            $table->string('email')->nullable();
            $table->string('telefone')->nullable();

            // Endereço
            $table->string('cep', 10)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
