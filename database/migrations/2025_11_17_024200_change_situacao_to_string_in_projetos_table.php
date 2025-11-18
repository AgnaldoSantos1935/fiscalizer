<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ignora em SQLite (ambiente de testes) que não suporta MODIFY
        if (config('database.default') === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE `projetos` MODIFY `situacao` VARCHAR(50) NOT NULL DEFAULT 'planejado'");
    }

    public function down(): void
    {
        if (config('database.default') === 'sqlite') {
            return;
        }
        DB::statement(
            "ALTER TABLE `projetos` MODIFY `situacao` ENUM('analise','planejado','em_execucao','homologacao','aguardando_pagamento','concluido','suspenso','cancelado') NOT NULL DEFAULT 'planejado'"
        );
    }
};
