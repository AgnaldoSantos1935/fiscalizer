<?php

namespace App\Services;

use App\Models\Monitoramento;
use App\Models\MonitoramentoLog;
use Illuminate\Support\Facades\Http;

class MonitoramentoService
{
    public static function testar(Monitoramento $item)
    {
        $inicio = microtime(true);

        try {
            if ($item->tipo === 'ip') {
                // 🔹 Teste via socket ou ping
                if ($item->porta) {
                    $fp = @fsockopen($item->alvo, $item->porta, $errno, $errstr, 2);
                    if ($fp) {
                        fclose($fp);
                        $latencia = round((microtime(true) - $inicio) * 1000, 2);
                        $item->update([
                            'online' => true,
                            'latencia' => $latencia,
                            'erro' => null,
                            'ultima_verificacao' => now(),
                        ]);
                    } else {
                        $item->update([
                            'online' => false,
                            'latencia' => null,
                            'erro' => $errstr,
                            'ultima_verificacao' => now(),
                        ]);
                    }
                } else {
                    $command = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                        ? "ping -n 1 {$item->alvo}"
                        : "ping -c 1 {$item->alvo}";
                    exec($command, $output, $result);
                    if ($result === 0) {
                        preg_match('/time[=<]\s*([\d.]+)/', implode(' ', $output), $matches);
                        $tempo = $matches[1] ?? null;
                        $item->update([
                            'online' => true,
                            'latencia' => $tempo,
                            'erro' => null,
                            'ultima_verificacao' => now(),
                        ]);
                    } else {
                        $item->update([
                            'online' => false,
                            'latencia' => null,
                            'erro' => 'Sem resposta ao ping',
                            'ultima_verificacao' => now(),
                        ]);
                    }
                }
            } else {
                // 🔹 Teste via HTTP (para links e APIs)
                $response = Http::timeout(5)->get($item->alvo);
                $latencia = round((microtime(true) - $inicio) * 1000, 2);

                $item->update([
                    'online' => $response->successful(),
                    'status_code' => $response->status(),
                    'latencia' => $latencia,
                    'erro' => null,
                    'ultima_verificacao' => now(),
                ]);
            }
        } catch (\Exception $e) {
            $item->update([
                'online' => false,
                'erro' => $e->getMessage(),
                'latencia' => null,
                'ultima_verificacao' => now(),
            ]);
        }
        // Após atualizar o monitoramento:
        MonitoramentoLog::create([
            'monitoramento_id' => $item->id,
            'online' => $item->online,
            'status_code' => $item->status_code,
            'latencia' => $item->latencia,
            'erro' => $item->erro,
            'verificado_em' => now(),
        ]);

        $falhasConsecutivas = $item->logs()
            ->orderByDesc('id')
            ->take(3)
            ->where('online', false)
            ->count();

        if ($falhasConsecutivas >= 3) {
            // Dispara alerta
            \Illuminate\Support\Facades\Notification::route('mail', 'fiscal@seduc.pa.gov.br')
                ->notify(new \App\Notifications\FalhaConsecutivaNotification($item, $falhasConsecutivas));
        }

    }
}
