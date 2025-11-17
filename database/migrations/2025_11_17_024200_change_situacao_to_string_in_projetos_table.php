<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Altera o tipo da coluna 'situacao' de ENUM para STRING (VARCHAR)
        DB::statement("ALTER TABLE `projetos` MODIFY `situacao` VARCHAR(50) NOT NULL DEFAULT 'planejado'");
    }

    public function down(): void
    {
        // Reverte para ENUM com os valores originais
        DB::statement(
            "ALTER TABLE `projetos` MODIFY `situacao` ENUM('analise','planejado','em_execucao','homologacao','aguardando_pagamento','concluido','suspenso','cancelado') NOT NULL DEFAULT 'planejado'"
        );
    }
};