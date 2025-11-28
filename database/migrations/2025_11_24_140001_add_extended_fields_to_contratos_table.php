<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contratos')) {
            return;
        }

        Schema::table('contratos', function (Blueprint $table) {
            if (! Schema::hasColumn('contratos', 'ano')) {
                $table->integer('ano')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'processo_administrativo')) {
                $table->string('processo_administrativo')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'fundamentacao_legal')) {
                $table->text('fundamentacao_legal')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'contratante_json')) {
                $table->json('contratante_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'contratada_representante_json')) {
                $table->json('contratada_representante_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'vigencia_info_json')) {
                $table->json('vigencia_info_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'dotacao_orcamentaria_json')) {
                $table->json('dotacao_orcamentaria_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'reajuste_json')) {
                $table->json('reajuste_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'garantia_json')) {
                $table->json('garantia_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'pagamento_json')) {
                $table->json('pagamento_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'fiscalizacao_json')) {
                $table->json('fiscalizacao_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'penalidades_json')) {
                $table->json('penalidades_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'rescisao_json')) {
                $table->json('rescisao_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'lgpd_json')) {
                $table->json('lgpd_json')->nullable();
            }
            if (! Schema::hasColumn('contratos', 'publicacao_doe_json')) {
                $table->json('publicacao_doe_json')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('contratos')) {
            return;
        }

        Schema::table('contratos', function (Blueprint $table) {
            $cols = [
                'ano',
                'processo_administrativo',
                'fundamentacao_legal',
                'contratante_json',
                'contratada_representante_json',
                'vigencia_info_json',
                'dotacao_orcamentaria_json',
                'reajuste_json',
                'garantia_json',
                'pagamento_json',
                'fiscalizacao_json',
                'penalidades_json',
                'rescisao_json',
                'lgpd_json',
                'publicacao_doe_json',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('contratos', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};

