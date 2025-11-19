<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            // 8.1 Forma de entrega
            $table->string('entrega_forma')->nullable(); // total | parcelada
            $table->integer('entrega_parcelas_quantidade')->nullable();
            $table->integer('entrega_primeira_em_dias')->nullable();
            $table->integer('entrega_aviso_antecedencia_dias')->nullable();
            // 8.2 Recebimento do bem
            $table->text('recebimento_endereco')->nullable();
            $table->string('recebimento_horario')->nullable(); // HH:mm
            // 8.3 Prazo máximo de validade
            $table->integer('validade_minima_entrega_dias')->nullable();
            // 9.1 Prazo do contrato
            $table->string('prazo_contrato')->nullable(); // 30_dias | 12_meses
            // 9.2 Possibilidade de prorrogação
            $table->boolean('prorrogacao_possivel')->nullable();
            // 9.3 Forma de pagamento
            $table->string('pagamento_meio')->nullable(); // ordem_bancaria
            $table->string('pagamento_onde')->nullable();
            $table->integer('pagamento_prazo_dias')->nullable();
            $table->string('regularidade_fiscal_prova_tipo')->nullable(); // sicaf_ou_cul | art68_documentos
            // 9.4 Garantia do contrato
            $table->string('garantia_contrato_tipo')->nullable(); // percentual | nao_ha
            $table->decimal('garantia_contrato_percentual', 5, 2)->nullable();
            $table->text('garantia_contrato_justificativa')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->dropColumn([
                'entrega_forma',
                'entrega_parcelas_quantidade',
                'entrega_primeira_em_dias',
                'entrega_aviso_antecedencia_dias',
                'recebimento_endereco',
                'recebimento_horario',
                'validade_minima_entrega_dias',
                'prazo_contrato',
                'prorrogacao_possivel',
                'pagamento_meio',
                'pagamento_onde',
                'pagamento_prazo_dias',
                'regularidade_fiscal_prova_tipo',
                'garantia_contrato_tipo',
                'garantia_contrato_percentual',
                'garantia_contrato_justificativa',
            ]);
        });
    }
};
