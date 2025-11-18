<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🚀 Adiciona campos de controle de expiração e troca de senha
     *
     * Esta migration é 100% retrocompatível:
     * não remove dados existentes e não altera colunas antigas.
     * Pode ser executada com segurança em uma base já populada.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 🔹 Data de expiração da senha (nullable → não afeta usuários antigos)
            if (! Schema::hasColumn('users', 'password_expires_at')) {
                $table->timestamp('password_expires_at')->nullable()->after('password');
            }

            // 🔹 Indica se o usuário precisa trocar a senha no próximo login
            if (! Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('password_expires_at');
            }
        });
    }

    /**
     * 🧩 Rollback seguro
     *
     * Remove apenas as colunas criadas por esta migration.
     * Nenhum outro dado é afetado.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'password_expires_at')) {
                $table->dropColumn('password_expires_at');
            }
            if (Schema::hasColumn('users', 'must_change_password')) {
                $table->dropColumn('must_change_password');
            }
        });
    }
};
