<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            // Permite criar documento antes de existir o contrato
            $table->unsignedBigInteger('contrato_id')->nullable()->change();

            // Troca ENUM por STRING para aceitar valores como 'contrato_pdf'
            $table->string('tipo', 50)->default('OUTROS')->change();
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            // Restaura NOT NULL (pode falhar se houver registros com NULL)
            $table->unsignedBigInteger('contrato_id')->nullable(false)->change();
        });

        // Volta 'tipo' para ENUM original
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE `documentos` MODIFY `tipo` ENUM('TR','ETP','PARECER','NOTA_TECNICA','RELATORIO','OUTROS') NOT NULL DEFAULT 'OUTROS'"
        );
    }
};