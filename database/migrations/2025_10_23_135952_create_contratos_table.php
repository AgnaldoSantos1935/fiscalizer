<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('contratos', function (Blueprint $table) {
        $table->id();
        $table->string('numero', 30);
        $table->text('objeto');
        $table->foreignId('contratada_id')->constrained('empresas');
        $table->decimal('valor_global', 14, 2)->default(0);
        $table->date('data_inicio')->nullable();
        $table->date('data_fim')->nullable();
        $table->enum('situacao', ['vigente','encerrado','rescindido','suspenso'])->default('vigente');
        $table->foreignId('gestor_id')->nullable()->constrained('usuarios');
        $table->foreignId('fiscal_id')->nullable()->constrained('usuarios');
        $table->enum('tipo', ['TI','Serviço','Obra','Material'])->default('TI');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
