<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();
            $table->string('nome');                  // Nome do host (ex: Servidor GLPI)
            $table->string('endereco');              // IP ou domínio
            $table->enum('tipo', ['ip', 'link'])->default('link'); // Tipo de host
            $table->integer('porta')->nullable();    // Porta opcional
            $table->string('localizacao')->nullable(); // Ex: SEDUC, DRE etc.
            $table->text('descricao')->nullable();   // Observações gerais
            $table->boolean('ativo')->default(true); // Se deve ser monitorado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosts');
    }
};
