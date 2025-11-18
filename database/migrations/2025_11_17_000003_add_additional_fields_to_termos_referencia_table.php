<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->text('modelo_execucao')->nullable();
            $table->text('modelo_gestao')->nullable();
            $table->text('criterios_medicao_pagamento')->nullable();
            $table->text('forma_criterios_selecao_fornecedor')->nullable();
            $table->text('especificacao_produto')->nullable();
            $table->text('locais_entrega_recebimento')->nullable();
            $table->text('garantia_manutencao_assistencia')->nullable();
            $table->text('estimativas_valor_texto')->nullable();
            $table->text('adequacao_orcamentaria')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->dropColumn([
                'modelo_execucao',
                'modelo_gestao',
                'criterios_medicao_pagamento',
                'forma_criterios_selecao_fornecedor',
                'especificacao_produto',
                'locais_entrega_recebimento',
                'garantia_manutencao_assistencia',
                'estimativas_valor_texto',
                'adequacao_orcamentaria',
            ]);
        });
    }
};