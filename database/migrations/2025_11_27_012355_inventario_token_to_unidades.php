<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('unidades')) {
            Schema::table('unidades', function (Blueprint $table) {
                if (! Schema::hasColumn('unidades', 'inventario_token')) {
                    $table->string('inventario_token')->nullable()->unique();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('unidades') && Schema::hasColumn('unidades', 'inventario_token')) {
            Schema::table('unidades', function (Blueprint $table) {
                $table->dropColumn('inventario_token');
            });
        }
    }
};

