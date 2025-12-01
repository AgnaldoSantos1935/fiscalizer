<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('escolas', function (Blueprint $table) {
            if (! Schema::hasColumn('escolas', 'dre')) {
                $table->string('dre', 32)->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('escolas', function (Blueprint $table) {
            if (Schema::hasColumn('escolas', 'dre')) {
                $table->dropIndex(['dre']);
                $table->dropColumn('dre');
            }
        });
    }
};
