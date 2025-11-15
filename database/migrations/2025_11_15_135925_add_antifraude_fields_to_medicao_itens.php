<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medicao_itens', function (Blueprint $table) {

            $table->unsignedBigInteger('demanda_id')->nullable()->after('medicao_id');
            $table->unsignedBigInteger('requisito_id')->nullable()->after('demanda_id');

            $table->unsignedBigInteger('sistema_id')->nullable()->after('requisito_id');
            $table->unsignedBigInteger('modulo_id')->nullable()->after('sistema_id');

            $table->string('tipo_manutencao')->nullable()->after('modulo_id');

            $table->string('item_unico_hash', 80)->nullable()->unique()->after('tipo_manutencao')
                ->comment('hash antifraude para detectar duplicidade de medição');

            // FKs opcionais
            $table->foreign('demanda_id')->references('id')->on('demandas')->nullOnDelete();
            $table->foreign('requisito_id')->references('id')->on('requisitos')->nullOnDelete();
            $table->foreign('sistema_id')->references('id')->on('sistemas')->nullOnDelete();
            $table->foreign('modulo_id')->references('id')->on('modulos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('medicao_itens', function (Blueprint $table) {
            $table->dropColumn([
                'demanda_id',
                'requisito_id',
                'sistema_id',
                'modulo_id',
                'tipo_manutencao',
                'item_unico_hash'
            ]);
        });
    }
};
