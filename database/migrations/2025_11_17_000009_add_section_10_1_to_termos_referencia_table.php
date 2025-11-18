<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->string('funcional_programatica', 100)->nullable();
            $table->string('elemento_despesa', 50)->nullable();
            $table->string('fonte_recurso', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->dropColumn(['funcional_programatica','elemento_despesa','fonte_recurso']);
        });
    }
};