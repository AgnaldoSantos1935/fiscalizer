<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Garantir coluna email_institucional
        if (! Schema::hasColumn('user_profiles', 'email_institucional')) {
            Schema::table('user_profiles', function (Blueprint $table) {
                $table->string('email_institucional')->nullable()->after('celular');
            });
        }

        // Migrar dados de email_pessoal para email_institucional, sem perder dados
        if (Schema::hasColumn('user_profiles', 'email_pessoal')) {
            DB::statement('UPDATE user_profiles SET email_institucional = COALESCE(email_institucional, email_pessoal)');

            // Remover coluna email_pessoal
            Schema::table('user_profiles', function (Blueprint $table) {
                $table->dropColumn('email_pessoal');
            });
        }

        // Remover campos despresíveis
        if (Schema::hasColumn('user_profiles', 'cor_preferida')) {
            Schema::table('user_profiles', function (Blueprint $table) {
                $table->dropColumn('cor_preferida');
            });
        }
        if (Schema::hasColumn('user_profiles', 'signo')) {
            Schema::table('user_profiles', function (Blueprint $table) {
                $table->dropColumn('signo');
            });
        }
    }

    public function down(): void
    {
        // Recriar colunas removidas
        Schema::table('user_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('user_profiles', 'email_pessoal')) {
                $table->string('email_pessoal')->nullable()->after('celular');
            }
            if (! Schema::hasColumn('user_profiles', 'cor_preferida')) {
                $table->string('cor_preferida', 20)->nullable();
            }
            if (! Schema::hasColumn('user_profiles', 'signo')) {
                $table->string('signo', 20)->nullable();
            }
        });

        // Copiar de volta institucional -> pessoal (para rollback)
        DB::statement('UPDATE user_profiles SET email_pessoal = email_institucional WHERE email_institucional IS NOT NULL');
    }
};
