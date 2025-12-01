<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('contrato_itens', 'meses')) {
            Schema::table('contrato_itens', function (Blueprint $table) {
                $table->unsignedInteger('meses')->nullable()->after('quantidade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('contrato_itens', 'meses')) {
            Schema::table('contrato_itens', function (Blueprint $table) {
                $table->dropColumn('meses');
            });
        }
    }
};

