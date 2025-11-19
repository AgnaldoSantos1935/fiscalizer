<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('monitoramentos') && ! Schema::hasColumn('monitoramentos', 'host_id')) {
            Schema::table('monitoramentos', function (Blueprint $table) {
                // adiciona FK para hosts; mantém nullable para evitar falhas em dados legados
                $table->foreignId('host_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('hosts')
                    ->onDelete('cascade');

                // índice auxiliar (caso não seja criado automaticamente pelo FK)
                $table->index('host_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('monitoramentos') && Schema::hasColumn('monitoramentos', 'host_id')) {
            Schema::table('monitoramentos', function (Blueprint $table) {
                // drop FK e coluna
                $table->dropForeign(['host_id']);
                $table->dropIndex(['host_id']);
                $table->dropColumn('host_id');
            });
        }
    }
};