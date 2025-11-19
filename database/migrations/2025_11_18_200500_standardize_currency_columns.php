<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Padroniza colunas monetárias para DECIMAL(18,2)
        if (Schema::hasTable('termos_referencia')) {
            DB::statement('ALTER TABLE `termos_referencia` MODIFY `valor_estimado` DECIMAL(18,2) NULL');
        }
        if (Schema::hasTable('termos_referencia_itens')) {
            DB::statement('ALTER TABLE `termos_referencia_itens` MODIFY `valor_unitario` DECIMAL(18,2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE `termos_referencia_itens` MODIFY `valor_total` DECIMAL(18,2) NOT NULL DEFAULT 0');
        }

        if (Schema::hasTable('projeto_software')) {
            DB::statement('ALTER TABLE `projeto_software` MODIFY `valor_estimado` DECIMAL(18,2) NULL');
        }

        if (Schema::hasTable('empenho_items')) {
            DB::statement('ALTER TABLE `empenho_items` MODIFY `valor_unitario` DECIMAL(18,2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE `empenho_items` MODIFY `valor_total` DECIMAL(18,2) NOT NULL DEFAULT 0');
        }

        if (Schema::hasTable('medicao')) {
            DB::statement('ALTER TABLE `medicao` MODIFY `valor_total` DECIMAL(18,2) NOT NULL DEFAULT 0');
        }
        if (Schema::hasTable('boletim_medicaos')) {
            DB::statement('ALTER TABLE `boletim_medicaos` MODIFY `valor_total` DECIMAL(18,2) NOT NULL DEFAULT 0');
        }

        if (Schema::hasTable('medicao_notas_fiscais')) {
            DB::statement('ALTER TABLE `medicao_notas_fiscais` MODIFY `valor` DECIMAL(18,2) NOT NULL');
        }

        if (Schema::hasTable('pagamentos')) {
            DB::statement('ALTER TABLE `pagamentos` MODIFY `valor_pagamento` DECIMAL(18,2) NOT NULL');
        }
    }

    public function down(): void
    {
        // Retorna aos tamanhos anteriores (quando conhecidos)
        if (Schema::hasTable('termos_referencia')) {
            DB::statement('ALTER TABLE `termos_referencia` MODIFY `valor_estimado` DECIMAL(15,2) NULL');
        }
        if (Schema::hasTable('termos_referencia_itens')) {
            DB::statement('ALTER TABLE `termos_referencia_itens` MODIFY `valor_unitario` DECIMAL(15,2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE `termos_referencia_itens` MODIFY `valor_total` DECIMAL(15,2) NOT NULL DEFAULT 0');
        }

        if (Schema::hasTable('projeto_software')) {
            DB::statement('ALTER TABLE `projeto_software` MODIFY `valor_estimado` DECIMAL(14,2) NULL');
        }

        if (Schema::hasTable('empenho_items')) {
            DB::statement('ALTER TABLE `empenho_items` MODIFY `valor_unitario` DECIMAL(14,2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE `empenho_items` MODIFY `valor_total` DECIMAL(14,2) NOT NULL DEFAULT 0');
        }

        if (Schema::hasTable('medicao')) {
            DB::statement('ALTER TABLE `medicao` MODIFY `valor_total` DECIMAL(14,2) NOT NULL DEFAULT 0');
        }
        if (Schema::hasTable('boletim_medicaos')) {
            DB::statement('ALTER TABLE `boletim_medicaos` MODIFY `valor_total` DECIMAL(15,2) NOT NULL DEFAULT 0');
        }

        if (Schema::hasTable('medicao_notas_fiscais')) {
            DB::statement('ALTER TABLE `medicao_notas_fiscais` MODIFY `valor` DECIMAL(12,2) NOT NULL');
        }

        if (Schema::hasTable('pagamentos')) {
            DB::statement('ALTER TABLE `pagamentos` MODIFY `valor_pagamento` DECIMAL(15,2) NOT NULL');
        }
    }
};