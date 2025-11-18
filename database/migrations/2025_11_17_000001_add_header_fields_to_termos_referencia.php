<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->string('tipo_tr')->nullable()->after('titulo');
            $table->string('pae_numero')->nullable()->after('tipo_tr');
            $table->string('cidade')->nullable()->after('pae_numero');
            $table->date('data_emissao')->nullable()->after('cidade');
            $table->string('responsavel_nome')->nullable()->after('data_emissao');
            $table->string('responsavel_cargo')->nullable()->after('responsavel_nome');
            $table->string('responsavel_matricula')->nullable()->after('responsavel_cargo');
        });
    }

    public function down(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_tr',
                'pae_numero',
                'cidade',
                'data_emissao',
                'responsavel_nome',
                'responsavel_cargo',
                'responsavel_matricula',
            ]);
        });
    }
};