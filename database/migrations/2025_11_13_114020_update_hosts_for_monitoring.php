<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    // Adiciona colunas apenas se ainda não existirem
    if (!Schema::hasColumn('hosts', 'tipo_monitoramento')) {
        Schema::table('hosts', function (Blueprint $table) {
            $table->enum('tipo_monitoramento', [
                'ping', 'porta', 'http', 'snmp', 'mikrotik', 'speedtest'
            ])->default('ping')->after('status');
        });
    }

    if (!Schema::hasColumn('hosts', 'host_alvo')) {
        Schema::table('hosts', function (Blueprint $table) {
            $table->string('host_alvo')->nullable()->after('tipo_monitoramento');
        });
    }

    if (!Schema::hasColumn('hosts', 'snmp_community')) {
        Schema::table('hosts', function (Blueprint $table) {
            $table->string('snmp_community')->nullable()->after('host_alvo');
        });
    }

    if (!Schema::hasColumn('hosts', 'mikrotik_user')) {
        Schema::table('hosts', function (Blueprint $table) {
            $table->string('mikrotik_user')->nullable()->after('snmp_community');
        });
    }

    if (!Schema::hasColumn('hosts', 'mikrotik_pass')) {
        Schema::table('hosts', function (Blueprint $table) {
            $table->string('mikrotik_pass')->nullable()->after('mikrotik_user');
        });
    }

    if (!Schema::hasColumn('hosts', 'config_extra')) {
        Schema::table('hosts', function (Blueprint $table) {
            $table->json('config_extra')->nullable()->after('mikrotik_pass');
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove colunas apenas se existirem
        if (Schema::hasColumn('hosts', 'config_extra')) {
            Schema::table('hosts', function (Blueprint $table) { $table->dropColumn('config_extra'); });
        }
        if (Schema::hasColumn('hosts', 'mikrotik_pass')) {
            Schema::table('hosts', function (Blueprint $table) { $table->dropColumn('mikrotik_pass'); });
        }
        if (Schema::hasColumn('hosts', 'mikrotik_user')) {
            Schema::table('hosts', function (Blueprint $table) { $table->dropColumn('mikrotik_user'); });
        }
        if (Schema::hasColumn('hosts', 'snmp_community')) {
            Schema::table('hosts', function (Blueprint $table) { $table->dropColumn('snmp_community'); });
        }
        if (Schema::hasColumn('hosts', 'host_alvo')) {
            Schema::table('hosts', function (Blueprint $table) { $table->dropColumn('host_alvo'); });
        }
        if (Schema::hasColumn('hosts', 'tipo_monitoramento')) {
            Schema::table('hosts', function (Blueprint $table) { $table->dropColumn('tipo_monitoramento'); });
        }
    }
};
