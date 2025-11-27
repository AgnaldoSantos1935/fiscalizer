<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hosts') && ! Schema::hasColumn('hosts', 'unidade_id')) {
            Schema::table('hosts', function (Blueprint $table) {
                $table->foreignId('unidade_id')->nullable()->constrained('unidades')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hosts') && Schema::hasColumn('hosts', 'unidade_id')) {
            Schema::table('hosts', function (Blueprint $table) {
                $table->dropForeign(['unidade_id']);
                $table->dropColumn('unidade_id');
            });
        }
    }
};

