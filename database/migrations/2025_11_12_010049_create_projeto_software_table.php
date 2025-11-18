<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projetos_software', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique(); // ex: APF0132
            $table->string('titulo');
            $table->string('sistema', 120)->nullable(); // ex: SIGEP
            $table->string('modulo', 120)->nullable(); // ex: Planejamento de Matrícula
            $table->string('submodulo', 120)->nullable(); // ex: Dashboards
            $table->string('solicitante')->nullable(); // CEMEC/DPLAN/SEDUC
            $table->string('fornecedor')->nullable(); // Montreal Informática S.A.
            $table->decimal('pontos_funcao', 8, 2)->nullable();
            $table->date('data_solicitacao')->nullable();
            $table->date('data_homologacao')->nullable();
            $table->enum('situacao', ['Analise', 'Em Execucao', 'Homologado', 'Pago', 'Suspenso'])->default('Analise');
            $table->decimal('valor_estimado', 14, 2)->nullable();
            $table->unsignedBigInteger('contrato_id')->nullable();
            $table->foreign('contrato_id')->references('id')->on('contratos')->nullOnDelete();
            $table->timestamps();
            $table->index(['codigo', 'sistema', 'modulo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projetos_software');
    }
};
