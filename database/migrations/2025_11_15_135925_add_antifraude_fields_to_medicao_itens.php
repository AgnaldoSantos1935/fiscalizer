<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicao_itens', function (Blueprint $table) {
            // Adiciona colunas somente se ainda não existem (ambientes já populados)
            if (! Schema::hasColumn('medicao_itens', 'demanda_id')) {
                $table->unsignedBigInteger('demanda_id')->nullable()->after('medicao_id');
                $table->foreign('demanda_id')->references('id')->on('demandas')->nullOnDelete();
            }

            if (! Schema::hasColumn('medicao_itens', 'requisito_id')) {
                $table->unsignedBigInteger('requisito_id')->nullable()->after('demanda_id');
                $table->foreign('requisito_id')->references('id')->on('requisitos')->nullOnDelete();
            }

            if (! Schema::hasColumn('medicao_itens', 'sistema_id')) {
                $table->unsignedBigInteger('sistema_id')->nullable()->after('requisito_id');
                $table->foreign('sistema_id')->references('id')->on('sistemas')->nullOnDelete();
            }

            if (! Schema::hasColumn('medicao_itens', 'modulo_id')) {
                $table->unsignedBigInteger('modulo_id')->nullable()->after('sistema_id');
                $table->foreign('modulo_id')->references('id')->on('modulos')->nullOnDelete();
            }

            if (! Schema::hasColumn('medicao_itens', 'tipo_manutencao')) {
                $table->string('tipo_manutencao')->nullable()->after('modulo_id');
            }

            if (! Schema::hasColumn('medicao_itens', 'item_unico_hash')) {
                $table->string('item_unico_hash', 80)->nullable()->unique()->after('tipo_manutencao')
                    ->comment('hash antifraude para detectar duplicidade de medição');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medicao_itens', function (Blueprint $table) {
            // Remoções seguras: solta FKs e apaga colunas apenas se existirem
            if (Schema::hasColumn('medicao_itens', 'demanda_id')) {
                // Nome padrão de constraint: <tabela>_<coluna>_foreign
                $table->dropForeign('medicao_itens_demanda_id_foreign');
                $table->dropColumn('demanda_id');
            }

            if (Schema::hasColumn('medicao_itens', 'requisito_id')) {
                $table->dropForeign('medicao_itens_requisito_id_foreign');
                $table->dropColumn('requisito_id');
            }

            if (Schema::hasColumn('medicao_itens', 'sistema_id')) {
                $table->dropForeign('medicao_itens_sistema_id_foreign');
                $table->dropColumn('sistema_id');
            }

            if (Schema::hasColumn('medicao_itens', 'modulo_id')) {
                $table->dropForeign('medicao_itens_modulo_id_foreign');
                $table->dropColumn('modulo_id');
            }

            if (Schema::hasColumn('medicao_itens', 'tipo_manutencao')) {
                $table->dropColumn('tipo_manutencao');
            }

            if (Schema::hasColumn('medicao_itens', 'item_unico_hash')) {
                $table->dropColumn('item_unico_hash');
            }
        });
    }
};
