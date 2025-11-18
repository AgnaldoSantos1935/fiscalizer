<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->boolean('prova_qualidade')->nullable()->after('adequacao_orcamentaria_confirmada');
            $table->text('prova_qualidade_justificativa')->nullable()->after('prova_qualidade');
        });
    }

    public function down(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->dropColumn(['prova_qualidade', 'prova_qualidade_justificativa']);
        });
    }
};