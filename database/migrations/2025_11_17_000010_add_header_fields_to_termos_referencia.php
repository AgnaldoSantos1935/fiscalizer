<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('termos_referencia')) {
            Schema::table('termos_referencia', function (Blueprint $table) {
                if (! Schema::hasColumn('termos_referencia', 'tipo_tr')) {
                    $table->string('tipo_tr')->nullable()->after('titulo');
                }
                if (! Schema::hasColumn('termos_referencia', 'pae_numero')) {
                    $table->string('pae_numero')->nullable()->after('tipo_tr');
                }
                if (! Schema::hasColumn('termos_referencia', 'cidade')) {
                    $table->string('cidade')->nullable()->after('pae_numero');
                }
                if (! Schema::hasColumn('termos_referencia', 'data_emissao')) {
                    $table->date('data_emissao')->nullable()->after('cidade');
                }
                if (! Schema::hasColumn('termos_referencia', 'responsavel_nome')) {
                    $table->string('responsavel_nome')->nullable()->after('data_emissao');
                }
                if (! Schema::hasColumn('termos_referencia', 'responsavel_cargo')) {
                    $table->string('responsavel_cargo')->nullable()->after('responsavel_nome');
                }
                if (! Schema::hasColumn('termos_referencia', 'responsavel_matricula')) {
                    $table->string('responsavel_matricula')->nullable()->after('responsavel_cargo');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('termos_referencia')) {
            Schema::table('termos_referencia', function (Blueprint $table) {
                $cols = [
                    'tipo_tr',
                    'pae_numero',
                    'cidade',
                    'data_emissao',
                    'responsavel_nome',
                    'responsavel_cargo',
                    'responsavel_matricula',
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('termos_referencia', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
