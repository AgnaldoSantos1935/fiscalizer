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
    Schema::table('hosts', function (Blueprint $table) {

        // Tipo do teste
        $table->enum('tipo_monitoramento', [
            'ping', 'porta', 'http', 'snmp', 'mikrotik', 'speedtest'
        ])->default('ping')->after('status');

        // Alvo formal
        $table->string('host_alvo')->nullable()->after('tipo_monitoramento');

        // Credenciais SNMP
        $table->string('snmp_community')->nullable()->after('host_alvo');

        // Credenciais Mikrotik
        $table->string('mikrotik_user')->nullable()->after('snmp_community');
        $table->string('mikrotik_pass')->nullable()->after('mikrotik_user');

        // Config extra
        $table->json('config_extra')->nullable()->after('mikrotik_pass');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
