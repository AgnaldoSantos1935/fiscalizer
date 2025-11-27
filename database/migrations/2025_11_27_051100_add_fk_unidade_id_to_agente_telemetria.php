<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('agente_telemetria') && Schema::hasTable('unidades')) {
            Schema::table('agente_telemetria', function (Blueprint $table) {
                if (Schema::hasColumn('agente_telemetria', 'unidade_id')) {
                    $table->foreign('unidade_id')->references('id')->on('unidades')->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('agente_telemetria')) {
            Schema::table('agente_telemetria', function (Blueprint $table) {
                $table->dropForeign(['unidade_id']);
            });
        }
    }
};

