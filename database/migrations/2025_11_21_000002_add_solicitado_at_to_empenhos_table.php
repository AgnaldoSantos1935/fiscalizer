<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            $table->timestamp('solicitado_at')->nullable()->after('data_lancamento');
        });
    }

    public function down(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            $table->dropColumn('solicitado_at');
        });
    }
};
