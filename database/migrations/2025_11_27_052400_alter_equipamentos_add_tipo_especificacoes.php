<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('equipamentos')) {
            Schema::table('equipamentos', function (Blueprint $table) {
                if (! Schema::hasColumn('equipamentos', 'tipo')) {
                    $table->string('tipo')->nullable();
                }
                if (! Schema::hasColumn('equipamentos', 'especificacoes')) {
                    $table->json('especificacoes')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('equipamentos')) {
            Schema::table('equipamentos', function (Blueprint $table) {
                if (Schema::hasColumn('equipamentos', 'especificacoes')) {
                    $table->dropColumn('especificacoes');
                }
                if (Schema::hasColumn('equipamentos', 'tipo')) {
                    $table->dropColumn('tipo');
                }
            });
        }
    }
};
