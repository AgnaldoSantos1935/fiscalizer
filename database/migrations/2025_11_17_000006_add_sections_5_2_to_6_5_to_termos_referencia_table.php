<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            // 5.2 Amostra
            $table->boolean('edital_exigira_amostra')->nullable()->after('prova_qualidade_justificativa');
            $table->text('edital_amostra_justificativa')->nullable()->after('edital_exigira_amostra');

            // 5.3 Garantia do bem
            $table->boolean('garantia_bem')->nullable()->after('edital_amostra_justificativa');
            $table->string('garantia_bem_itens')->nullable()->after('garantia_bem');
            $table->unsignedInteger('garantia_bem_meses')->nullable()->after('garantia_bem_itens');

            // 5.4 Assistência técnica
            $table->string('assistencia_tecnica_tipo')->nullable()->after('garantia_bem_meses');
            $table->unsignedInteger('assistencia_tecnica_meses')->nullable()->after('assistencia_tecnica_tipo');

            // 6.1 Forma de contratação
            $table->string('forma_contratacao')->nullable()->after('assistencia_tecnica_meses');

            // 6.2 Critério de julgamento (seleção)
            $table->string('criterio_julgamento_tipo')->nullable()->after('forma_contratacao');

            // 6.3 Orçamento sigiloso
            $table->boolean('orcamento_sigiloso')->nullable()->after('criterio_julgamento_tipo');
            $table->text('orcamento_sigiloso_justificativa')->nullable()->after('orcamento_sigiloso');

            // 6.5 Itens exclusivos ME/EPP
            $table->boolean('itens_exclusivos_me_epp')->nullable()->after('orcamento_sigiloso_justificativa');
            $table->text('itens_exclusivos_lista')->nullable()->after('itens_exclusivos_me_epp');
        });
    }

    public function down(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->dropColumn([
                'edital_exigira_amostra', 'edital_amostra_justificativa',
                'garantia_bem', 'garantia_bem_itens', 'garantia_bem_meses',
                'assistencia_tecnica_tipo', 'assistencia_tecnica_meses',
                'forma_contratacao', 'criterio_julgamento_tipo',
                'orcamento_sigiloso', 'orcamento_sigiloso_justificativa',
                'itens_exclusivos_me_epp', 'itens_exclusivos_lista',
            ]);
        });
    }
};
