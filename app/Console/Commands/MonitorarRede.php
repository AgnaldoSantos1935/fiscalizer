<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Monitoramento;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MonitorarRede extends Command
{
    /**
     * Nome do comando (para execução manual ou agendada)
     */
    protected $signature = 'monitorar:rede';

    /**
     * Descrição
     */
    protected $description = 'Executa verificação periódica dos IPs e links da tabela monitoramentos.';

    public function handle()
    {
        $this->info('🔎 Iniciando rotina de monitoramento de rede...');
        $alvos = Monitoramento::where('ativo', true)->get();

        if ($alvos->isEmpty()) {
            $this->warn('⚠️ Nenhum monitoramento ativo encontrado.');
            return Command::SUCCESS;
        }

        foreach ($alvos as $item) {
            $this->line("🌐 Testando: {$item->nome} ({$item->alvo})");
            $inicio = microtime(true);
            $status = false;
            $codigo = null;
            $latencia = null;
            $erro = null;

            try {
                if ($item->tipo === 'ip') {
                    // 🔹 PING (comando compatível Windows/Linux)
                    $cmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
                        ? "ping -n 1 " . escapeshellarg($item->alvo)
                        : "ping -c 1 " . escapeshellarg($item->alvo);

                    $saida = [];
                    $retorno = 1;
                    @exec($cmd, $saida, $retorno);

                    $status = ($retorno === 0);
                    if ($status && preg_match('/time[=<]([\d\.]+)\s?ms/', implode(' ', $saida), $m)) {
                        $latencia = floatval($m[1]);
                    }
                } else {
                    // 🔹 HTTP / HTTPS
                    $inicio_http = microtime(true);
                    $resposta = Http::timeout(5)->get($item->alvo);
                    $fim_http = microtime(true);

                    $codigo = $resposta->status();
                    $status = $resposta->successful();
                    $latencia = round(($fim_http - $inicio_http) * 1000, 2);
                }
            } catch (\Throwable $e) {
                $erro = $e->getMessage();
                Log::error("Erro ao testar {$item->alvo}: {$erro}");
            }

            $item->update([
                'online' => $status,
                'status_code' => $codigo,
                'latencia' => $latencia,
                'erro' => $erro,
                'ultima_verificacao' => Carbon::now(),
            ]);
            // 🔹 Registra histórico da medição
\App\Models\MonitoramentoLog::create([
    'monitoramento_id' => $item->id,
    'online' => $status,
    'status_code' => $codigo,
    'latencia' => $latencia,
    'erro' => $erro,
    'data_teste' => Carbon::now(),
]);


            $resultado = $status ? '🟢 ONLINE' : '🔴 OFFLINE';
            $this->info("   → {$resultado} | Latência: " . ($latencia ? "{$latencia}ms" : '—') . " | HTTP: " . ($codigo ?? '—'));
        }

        $this->info('✅ Monitoramento concluído com sucesso!');
        return Command::SUCCESS;
    }
}
