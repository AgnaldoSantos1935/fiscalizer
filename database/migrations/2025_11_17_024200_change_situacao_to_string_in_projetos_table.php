<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = config('database.default');
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE projetos ALTER COLUMN situacao TYPE VARCHAR(50) USING situacao::text');
            DB::statement("ALTER TABLE projetos ALTER COLUMN situacao SET DEFAULT 'planejado'");
            DB::statement('ALTER TABLE projetos ALTER COLUMN situacao SET NOT NULL');

            return;
        }
        if ($driver === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE `projetos` MODIFY `situacao` VARCHAR(50) NOT NULL DEFAULT 'planejado'");
    }

    public function down(): void
    {
        $driver = config('database.default');
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE projetos ALTER COLUMN situacao DROP DEFAULT');

            return;
        }
        if ($driver === 'sqlite') {
            return;
        }
        DB::statement(
            "ALTER TABLE `projetos` MODIFY `situacao` ENUM('analise','planejado','em_execucao','homologacao','aguardando_pagamento','concluido','suspenso','cancelado') NOT NULL DEFAULT 'planejado'"
        );
    }
};
