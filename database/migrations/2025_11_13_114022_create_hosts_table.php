<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosts', function (Blueprint $table) {

            // Tipo de monitoramento: ping, porta, http, snmp, mikrotik, speedtest
            if (!Schema::hasColumn('hosts', 'tipo_monitoramento')) {
                $table->enum('tipo_monitoramento', [
                    'ping',
                    'porta',
                    'http',
                    'snmp',
                    'mikrotik',
                    'speedtest'
                ])->default('ping')->after('status');
            }

            // Alvo formal (IP/URL) — diferente de ip_atingivel
            if (!Schema::hasColumn('hosts', 'host_alvo')) {
                $table->string('host_alvo')->nullable()->after('tipo_monitoramento');
            }

            // Comunidade SNMP
            if (!Schema::hasColumn('hosts', 'snmp_community')) {
                $table->string('snmp_community')->nullable()->after('host_alvo');
            }

            // Credenciais Mikrotik
            if (!Schema::hasColumn('hosts', 'mikrotik_user')) {
                $table->string('mikrotik_user')->nullable()->after('snmp_community');
            }

            if (!Schema::hasColumn('hosts', 'mikrotik_pass')) {
                $table->string('mikrotik_pass')->nullable()->after('mikrotik_user');
            }

            // JSON extra para expandir configurações futuras
            if (!Schema::hasColumn('hosts', 'config_extra')) {
                $table->json('config_extra')->nullable()->after('mikrotik_pass');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hosts', function (Blueprint $table) {

            if (Schema::hasColumn('hosts', 'tipo_monitoramento')) {
                $table->dropColumn('tipo_monitoramento');
            }

            if (Schema::hasColumn('hosts', 'host_alvo')) {
                $table->dropColumn('host_alvo');
            }

            if (Schema::hasColumn('hosts', 'snmp_community')) {
                $table->dropColumn('snmp_community');
            }

            if (Schema::hasColumn('hosts', 'mikrotik_user')) {
                $table->dropColumn('mikrotik_user');
            }

            if (Schema::hasColumn('hosts', 'mikrotik_pass')) {
                $table->dropColumn('mikrotik_pass');
            }

            if (Schema::hasColumn('hosts', 'config_extra')) {
                $table->dropColumn('config_extra');
            }
        });
    }
};
