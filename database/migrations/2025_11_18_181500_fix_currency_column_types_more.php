<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE `servidores` MODIFY `salario` DECIMAL(18,2) NULL');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `medicoes` MODIFY `valor_bruto` DECIMAL(18,2) NULL');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `medicoes` MODIFY `valor_desconto` DECIMAL(18,2) NULL');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `medicoes` MODIFY `valor_liquido` DECIMAL(18,2) NULL');
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        // sem reversão
    }
};
