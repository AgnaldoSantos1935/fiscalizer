<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('servidores', function (Blueprint $table) {
            $table->id();

            // Cada servidor é uma pessoa
            $table->foreignId('pessoa_id')->constrained('pessoas')->onDelete('cascade');

            // Dados funcionais
            $table->string('matricula')->unique();
            $table->string('cargo')->nullable();
            $table->string('funcao')->nullable();
            $table->string('lotacao')->nullable();
            $table->date('data_admissao')->nullable();
            $table->enum('vinculo', ['efetivo','comissionado','temporario','terceirizado'])->nullable();
            $table->enum('situacao', ['ativo','inativo','afastado','aposentado'])->default('ativo');
            $table->decimal('salario', 12, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('servidores');
    }
};
