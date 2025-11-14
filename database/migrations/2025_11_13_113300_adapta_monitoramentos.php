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
    Schema::table('monitoramentos', function (Blueprint $table) {

        // Métricas avançadas
        $table->float('jitter')->nullable()->after('latencia');
        $table->float('perda_pacotes')->nullable()->after('jitter');
        $table->float('tempo_resposta')->nullable()->after('perda_pacotes');

        // SNMP / Mikrotik
        $table->integer('cpu')->nullable()->after('tempo_resposta');
        $table->bigInteger('memoria_usada')->nullable()->after('cpu');
        $table->bigInteger('memoria_total')->nullable()->after('memoria_usada');
        $table->bigInteger('rx_rate')->nullable()->after('memoria_total');
        $table->bigInteger('tx_rate')->nullable()->after('rx_rate');

        // Speedtest
        $table->float('download')->nullable()->after('tx_rate');
        $table->float('upload')->nullable()->after('download');

        // Campos de controle
        $table->integer('duracao_online')->default(0)->after('erro');
        $table->integer('duracao_offline')->default(0)->after('duracao_online');

        // JSON coringa
        $table->json('dados_extra')->nullable()->after('upload');
    });
}

};
