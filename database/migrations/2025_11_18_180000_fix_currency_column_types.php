<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Contratos
        try {
            DB::statement('ALTER TABLE `contratos` MODIFY `valor_global` DECIMAL(18,2) NULL');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `contratos` MODIFY `valor_mensal` DECIMAL(18,2) NULL');
        } catch (\Throwable $e) {
        }

        // Empenhos (duas possíveis estruturas)
        try {
            DB::statement('ALTER TABLE `empenhos` MODIFY `valor_total` DECIMAL(18,2) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `empenhos` MODIFY `valor` DECIMAL(18,2) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }

        // Pagamentos
        try {
            DB::statement('ALTER TABLE `pagamentos` MODIFY `valor_pagamento` DECIMAL(18,2) NOT NULL');
        } catch (\Throwable $e) {
        }

        // Itens de Empenho (nome utilizado nos migrations)
        try {
            DB::statement('ALTER TABLE `notas_empenho_itens` MODIFY `valor_unitario` DECIMAL(18,2) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `notas_empenho_itens` MODIFY `valor_total` DECIMAL(18,2) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }

        // Itens de Contrato
        try {
            DB::statement('ALTER TABLE `contrato_itens` MODIFY `valor_unitario` DECIMAL(18,2) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `contrato_itens` MODIFY `valor_total` DECIMAL(18,2) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }

        // Medição Itens (software)
        try {
            DB::statement('ALTER TABLE `medicao_itens` MODIFY `valor_unitario_pf` DECIMAL(18,2) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `medicao_itens` MODIFY `valor_unitario_ust` DECIMAL(18,2) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `medicao_itens` MODIFY `valor_total` DECIMAL(18,2) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }

        // Notas fiscais de medição
        try {
            DB::statement('ALTER TABLE `medicao_notas_fiscais` MODIFY `valor` DECIMAL(18,2) NOT NULL');
        } catch (\Throwable $e) {
        }

        // Ata Registro de Preços
        try {
            DB::statement('ALTER TABLE `atas_registro_precos` MODIFY `saldo_global` DECIMAL(18,2) NULL');
        } catch (\Throwable $e) {
        }

        // Adesões da Ata
        try {
            DB::statement('ALTER TABLE `ata_adesoes` MODIFY `valor_estimado` DECIMAL(18,2) NULL');
        } catch (\Throwable $e) {
        }

        // Ata Itens (mantém 4 casas para preço unitário)
        try {
            DB::statement('ALTER TABLE `ata_itens` MODIFY `saldo_disponivel` DECIMAL(18,2) NULL');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE `ata_itens` MODIFY `preco_unitario` DECIMAL(18,4) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        // Não reverte tipos por segurança
    }
};
