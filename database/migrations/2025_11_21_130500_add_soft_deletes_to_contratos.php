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
            if (! Schema::hasColumn('contratos', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('contratos')) {
            return;
        }

        Schema::table('contratos', function (Blueprint $table) {
            if (Schema::hasColumn('contratos', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
