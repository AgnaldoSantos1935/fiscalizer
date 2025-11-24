<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('equipes_projeto', function (Blueprint $table) {
            if (Schema::hasColumn('equipes_projeto', 'papel') && ! Schema::hasColumn('equipes_projeto', 'perfil')) {
                $table->renameColumn('papel', 'perfil');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipes_projeto', function (Blueprint $table) {
            if (Schema::hasColumn('equipes_projeto', 'perfil') && ! Schema::hasColumn('equipes_projeto', 'papel')) {
                $table->renameColumn('perfil', 'papel');
            }
        });
    }
};
