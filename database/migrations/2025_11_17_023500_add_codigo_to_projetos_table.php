<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona a coluna 'codigo' se ela não existir
        if (! Schema::hasColumn('projetos', 'codigo')) {
            Schema::table('projetos', function (Blueprint $table) {
                $table->string('codigo')->unique()->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        // Remove a coluna 'codigo' apenas se existir
        if (Schema::hasColumn('projetos', 'codigo')) {
            Schema::table('projetos', function (Blueprint $table) {
                $table->dropUnique(['codigo']);
                $table->dropColumn('codigo');
            });
        }
    }
};
