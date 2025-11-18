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
        if (Schema::hasTable('termos_referencia')) {
            Schema::table('termos_referencia', function (Blueprint $table) {
                $table->boolean('garantia_exigida')->nullable()->after('garantia_manutencao_assistencia');
                $table->boolean('manutencao_incluida')->nullable()->after('garantia_exigida');
                $table->boolean('assistencia_tecnica_incluida')->nullable()->after('manutencao_incluida');
                $table->boolean('adequacao_orcamentaria_confirmada')->nullable()->after('adequacao_orcamentaria');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('termos_referencia')) {
            Schema::table('termos_referencia', function (Blueprint $table) {
                $table->dropColumn([
                    'garantia_exigida',
                    'manutencao_incluida',
                    'assistencia_tecnica_incluida',
                    'adequacao_orcamentaria_confirmada',
                ]);
            });
        }
    }
};