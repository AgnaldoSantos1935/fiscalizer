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
        if (! Schema::hasTable('monitoramentos')) {
            return;
        }
        Schema::table('monitoramentos', function (Blueprint $table) {
            // Métricas avançadas
            if (! Schema::hasColumn('monitoramentos', 'jitter')) {
                $table->float('jitter')->nullable()->after('latencia');
            }
            if (! Schema::hasColumn('monitoramentos', 'perda_pacotes')) {
                $table->float('perda_pacotes')->nullable()->after('jitter');
            }
            if (! Schema::hasColumn('monitoramentos', 'tempo_resposta')) {
                $table->float('tempo_resposta')->nullable()->after('perda_pacotes');
            }

            // SNMP / Mikrotik
            if (! Schema::hasColumn('monitoramentos', 'cpu')) {
                $table->integer('cpu')->nullable()->after('tempo_resposta');
            }
            if (! Schema::hasColumn('monitoramentos', 'memoria_usada')) {
                $table->bigInteger('memoria_usada')->nullable()->after('cpu');
            }
            if (! Schema::hasColumn('monitoramentos', 'memoria_total')) {
                $table->bigInteger('memoria_total')->nullable()->after('memoria_usada');
            }
            if (! Schema::hasColumn('monitoramentos', 'rx_rate')) {
                $table->bigInteger('rx_rate')->nullable()->after('memoria_total');
            }
            if (! Schema::hasColumn('monitoramentos', 'tx_rate')) {
                $table->bigInteger('tx_rate')->nullable()->after('rx_rate');
            }

            // Speedtest
            if (! Schema::hasColumn('monitoramentos', 'download')) {
                $table->float('download')->nullable()->after('tx_rate');
            }
            if (! Schema::hasColumn('monitoramentos', 'upload')) {
                $table->float('upload')->nullable()->after('download');
            }

            // Campos de controle
            if (! Schema::hasColumn('monitoramentos', 'duracao_online')) {
                $table->integer('duracao_online')->default(0)->after('erro');
            }
            if (! Schema::hasColumn('monitoramentos', 'duracao_offline')) {
                $table->integer('duracao_offline')->default(0)->after('duracao_online');
            }

            // JSON coringa
            if (! Schema::hasColumn('monitoramentos', 'dados_extra')) {
                $table->json('dados_extra')->nullable()->after('upload');
            }
        });
    }
};
