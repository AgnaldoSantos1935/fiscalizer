<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('razao_social', 255);
            $table->string('nome_fantasia', 255)->nullable();
            $table->string('cnpj', 18)->unique();
            $table->string('inscricao_estadual', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 15)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
