<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projetos', function (Blueprint $table) {
            if (! Schema::hasColumn('projetos', 'codigo')) {
                $table->string('codigo')->unique()->nullable()->after('id');
            }
            if (! Schema::hasColumn('projetos', 'titulo')) {
                $table->string('titulo')->nullable()->after('codigo');
            }
            if (! Schema::hasColumn('projetos', 'sistema')) {
                $table->string('sistema')->nullable()->after('descricao');
            }
            if (! Schema::hasColumn('projetos', 'modulo')) {
                $table->string('modulo')->nullable()->after('sistema');
            }
            if (! Schema::hasColumn('projetos', 'pf_planejado')) {
                $table->decimal('pf_planejado', 10, 2)->default(0)->after('modulo');
            }
            if (! Schema::hasColumn('projetos', 'situacao')) {
                $table->enum('situacao', [
                    'analise',
                    'planejado',
                    'em_execucao',
                    'homologacao',
                    'aguardando_pagamento',
                    'concluido',
                    'suspenso',
                    'cancelado',
                ])->default('planejado')->after('data_fim');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projetos', function (Blueprint $table) {
            if (Schema::hasColumn('projetos', 'situacao')) {
                $table->dropColumn('situacao');
            }
            if (Schema::hasColumn('projetos', 'pf_planejado')) {
                $table->dropColumn('pf_planejado');
            }
            if (Schema::hasColumn('projetos', 'modulo')) {
                $table->dropColumn('modulo');
            }
            if (Schema::hasColumn('projetos', 'sistema')) {
                $table->dropColumn('sistema');
            }
            if (Schema::hasColumn('projetos', 'titulo')) {
                $table->dropColumn('titulo');
            }
            if (Schema::hasColumn('projetos', 'codigo')) {
                $table->dropUnique(['codigo']);
                $table->dropColumn('codigo');
            }
        });
    }
};
