<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('empenhos')) {
            return;
        }
        Schema::create('empenhos', function (Blueprint $table) {
            $table->id();

            // 🔗 Relações principais
            $table->foreignId('empenho_id')->nullable()->constrained('empenhos')->nullOnDelete();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('contrato_id')->nullable()->constrained('contratos')->nullOnDelete();

            // 📋 Dados da NE
            $table->string('numero', 30)->unique();
            $table->date('data_lancamento')->nullable();
            $table->string('processo', 50)->nullable();
            $table->string('programa_trabalho', 50)->nullable();
            $table->string('fonte_recurso', 50)->nullable();
            $table->string('natureza_despesa', 20)->nullable();
            $table->string('contrato_numero', 30)->nullable();
            $table->string('credor_nome', 150)->nullable();
            $table->string('cnpj', 20)->nullable();
            $table->decimal('valor_total', 14, 2)->default(0);
            $table->text('valor_extenso')->nullable();
            $table->string('ordenador_nome', 150)->nullable();
            $table->string('ordenador_cpf', 20)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empenhos');
    }
};
