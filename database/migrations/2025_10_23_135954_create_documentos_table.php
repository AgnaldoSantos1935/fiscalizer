<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('documentos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('contrato_id')->constrained('contratos');
        $table->enum('tipo', ['TR','ETP','Parecer','NotaTecnica','Relatorio','Outros'])->default('Outros');
        $table->string('titulo', 200)->nullable();
        $table->string('caminho_arquivo', 255)->nullable();
        $table->date('data_upload')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
