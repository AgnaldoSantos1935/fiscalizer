<?php

namespace App\Jobs;

use App\Models\Host;
use App\Models\MonitoramentoHost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class MonitorarHostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $hosts = Host::whereNotNull('ip_atingivel')->get();

        Log::info("🚀 Iniciando monitoramento automático de {$hosts->count()} hosts...");

        foreach ($hosts as $host) {
            $ip = $host->ip_atingivel;
            if (!$ip) continue;

            try {
                $inicio = microtime(true);

                // 🔹 PING (Linux)
                $process = new Process(['ping', '-c', '1', '-W', '2', $ip]);
                $process->run();

                $fim = microtime(true);
                $status = $process->isSuccessful() ? 'online' : 'offline';
                $tempo = $process->isSuccessful() ? round(($fim - $inicio) * 1000, 2) : null;

                // 🔹 Atualiza o status no hosts
                $host->update(['status' => $status]);

                // 🔹 Registra histórico
                MonitoramentoHost::create([
                    'host_id' => $host->id,
                    'ip' => $ip,
                    'status' => $status,
                    'tempo_resposta' => $tempo,
                    'saida_ping' => substr($process->getOutput(), 0, 500),
                    'verificado_em' => now(),
                ]);

                Log::info("🌐 {$host->nome_conexao} ({$ip}) → {$status} ({$tempo} ms)");
            } catch (\Exception $e) {
                Log::error("❌ Erro ao monitorar {$host->nome_conexao}: " . $e->getMessage());
            }
        }

        Log::info("✅ Monitoramento concluído.");
    }
}
