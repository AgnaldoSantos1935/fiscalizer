<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            // 🔗 Relação com usuários
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 🧍 Dados pessoais
            $table->string('nome_completo');
            $table->string('cpf', 14)->unique();
            $table->string('rg', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->unsignedTinyInteger('idade')->nullable();
            $table->enum('sexo', ['Masculino', 'Feminino', 'Outro'])->nullable();
            $table->string('signo', 20)->nullable();
            $table->string('mae')->nullable();
            $table->string('pai')->nullable();
            $table->string('tipo_sanguineo', 5)->nullable();
            $table->float('altura', 4, 2)->nullable(); // ex: 1.79
            $table->float('peso', 5, 2)->nullable();   // ex: 84.00
            $table->string('cor_preferida', 20)->nullable();

            // 🏠 Endereço
            $table->string('cep', 10)->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->default('Belém');
            $table->string('estado', 2)->default('PA');

            // ☎️ Contato
            $table->string('telefone_fixo', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email_pessoal')->nullable();
            $table->string('email_institucional')->nullable();

            // 💼 Dados funcionais (contexto institucional / Fiscalizer)
            $table->string('matricula')->nullable();
            $table->string('cargo')->nullable();
            $table->string('dre')->nullable();
            $table->string('lotacao')->nullable();
            $table->string('foto')->nullable();

            // 📄 Metadados e observações
            $table->text('observacoes')->nullable();
            $table->timestamp('data_atualizacao')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
