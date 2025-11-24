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
        // Evita erro quando a tabela já existe em ambientes com base pré-carregada
        if (Schema::hasTable('monitoramentos')) {
            return;
        }

        Schema::create('monitoramentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('host_id')
                ->constrained('hosts')
                ->onDelete('cascade');

            // Status básico
            $table->boolean('online')->default(false);
            $table->integer('status_code')->nullable();
            $table->float('latencia')->nullable();          // ms
            $table->float('jitter')->nullable();            // ms
            $table->float('perda_pacotes')->nullable();     // %

            // Tempo total gasto na checagem (ms)
            $table->float('tempo_resposta')->nullable();

            // Métricas SNMP / Mikrotik
            $table->integer('cpu')->nullable();             // %
            $table->bigInteger('memoria_usada')->nullable();
            $table->bigInteger('memoria_total')->nullable();
            $table->bigInteger('rx_rate')->nullable();      // bps
            $table->bigInteger('tx_rate')->nullable();      // bps

            // Speedtest (opcional)
            $table->float('download')->nullable();          // Mbps
            $table->float('upload')->nullable();            // Mbps

            // Erros e extras
            $table->text('erro')->nullable();
            $table->json('dados_extra')->nullable();

            // Controle de uptime/downtime
            $table->integer('duracao_online')->default(0);  // em segundos
            $table->integer('duracao_offline')->default(0); // em segundos

            $table->timestamp('ultima_verificacao')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoramentos');
    }
};
