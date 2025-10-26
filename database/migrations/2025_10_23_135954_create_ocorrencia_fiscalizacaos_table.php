<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('ocorrencias_fiscalizacao', function (Blueprint $table) {
        $table->id();
        $table->foreignId('contrato_id')->constrained('contratos');
        $table->date('data_ocorrencia')->nullable();
        $table->enum('tipo', ['advertencia','glosa','nao_conformidade','outros'])->default('outros');
        $table->text('descricao')->nullable();
        $table->foreignId('responsavel_id')->nullable()->constrained('usuarios');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocorrencia_fiscalizacaos');
    }
};
