<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            // 7.1 Habilitação jurídica
            $table->boolean('habilitacao_juridica_existencia')->nullable();
            $table->boolean('habilitacao_juridica_autorizacao')->nullable();
            // 7.2 Habilitação técnica
            $table->boolean('habilitacao_tecnica_exigida')->nullable();
            $table->text('habilitacao_tecnica_qual')->nullable();
            $table->text('habilitacao_tecnica_justificativa')->nullable();
            // 7.3 Qualificações técnicas exigidas
            $table->boolean('qt_declaracao_ciencia')->nullable();
            $table->text('qt_declaracao_justificativa')->nullable();
            $table->boolean('qt_registro_entidade')->nullable();
            $table->text('qt_registro_justificativa')->nullable();
            $table->boolean('qt_indicacao_pessoal')->nullable();
            $table->text('qt_indicacao_justificativa')->nullable();
            $table->boolean('qt_outro')->nullable();
            $table->text('qt_outro_especificar')->nullable();
            $table->text('qt_outro_justificativa')->nullable();
            $table->boolean('qt_nao_exigida')->nullable();
            // 7.4 Sustentabilidade
            $table->boolean('criterio_sustentabilidade')->nullable();
            $table->text('criterio_sustentabilidade_especificar')->nullable();
            // 7.5 Riscos assumidos pela contratada
            $table->boolean('riscos_assumidos_contratada')->nullable();
            $table->text('riscos_assumidos_especificar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->dropColumn([
                'habilitacao_juridica_existencia',
                'habilitacao_juridica_autorizacao',
                'habilitacao_tecnica_exigida',
                'habilitacao_tecnica_qual',
                'habilitacao_tecnica_justificativa',
                'qt_declaracao_ciencia',
                'qt_declaracao_justificativa',
                'qt_registro_entidade',
                'qt_registro_justificativa',
                'qt_indicacao_pessoal',
                'qt_indicacao_justificativa',
                'qt_outro',
                'qt_outro_especificar',
                'qt_outro_justificativa',
                'qt_nao_exigida',
                'criterio_sustentabilidade',
                'criterio_sustentabilidade_especificar',
                'riscos_assumidos_contratada',
                'riscos_assumidos_especificar',
            ]);
        });
    }
};