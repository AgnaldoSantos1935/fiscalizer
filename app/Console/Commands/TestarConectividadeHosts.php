<?php

namespace App\Console\Commands;

use App\Models\Host;
use App\Models\HostTeste;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TestarConectividadeHosts extends Command
{
    protected $signature = 'hosts:testar';

    protected $description = 'Executa testes de conectividade em todos os hosts ativos';

    public function handle()
    {
        $this->info('🚀 Iniciando testes de conectividade...');

        // Seleciona hosts válidos para teste: com IP e não marcados como inativos/manutenção
        $hosts = Host::query()
            ->whereNotNull('ip_atingivel')
            ->whereNotIn('status', ['inativo', 'em manutenção'])
            ->get();

        if ($hosts->isEmpty()) {
            $this->warn('⚠️ Nenhum host ativo encontrado para testar.');

            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($hosts->count());
        $bar->start();

        foreach ($hosts as $host) {
            try {
                $this->testarHost($host);
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\n❌ Erro ao testar host {$host->nome} ({$host->ip_atingivel}):
                 {$e->getMessage()}");
                Log::error("Erro no teste de conectividade do host {$host->id}", [
                    'host_id' => $host->id,
                    'erro' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $bar->finish();
        $this->info("\n✅ Testes concluídos para {$hosts->count()} hosts.");

        return Command::SUCCESS;
    }

    private function testarHost(Host $host): void
    {
        $ip = filter_var($host->ip_atingivel, FILTER_VALIDATE_IP);

        if (! $ip) {
            throw new \InvalidArgumentException("IP inválido: {$host->ip_atingivel}");
        }

        $sistemaOperacional = strtolower(PHP_OS_FAMILY);

        if ($sistemaOperacional === 'windows') {
            $comando = ['ping', '-n', '4', $ip];
        } else {
            $comando = ['ping', '-c', '4', '-W', '5', $ip];
        }

        $process = new Process($comando);
        $process->setTimeout(30);

        try {
            $inicio = microtime(true);
            $process->run();
            $duracao = microtime(true) - $inicio;

            $saida = $this->sanitizeOutput($process->getOutput());
            $saidaErro = $this->sanitizeOutput($process->getErrorOutput());

            $latencia = $this->extrairLatencia($saida);
            $perda = $this->extrairPerdaPacotes($saida, $sistemaOperacional);

            $statusConexao = $this->determinarStatusConexao($process, $perda);

            $ipOrigem = $this->obterIpOrigem();

            HostTeste::create([
                'host_id' => $host->id,
                'ip_destino' => $ip,
                'status_conexao' => $statusConexao,
                'latencia_ms' => $latencia,
                'perda_pacotes' => $perda,
                'ip_origem' => $ipOrigem,
                'modo_execucao' => 'agendado',
                'executado_por' => 'scheduler',
                'resultado_json' => [
                    'saida' => $saida,
                    'saida_erro' => $saidaErro,
                    'comando_executado' => $comando,
                    'exit_code' => $process->getExitCode(),
                    'tempo_execucao' => $duracao,
                ],
            ]);

        } catch (ProcessFailedException $e) {
            $duracao = isset($inicio) ? microtime(true) - $inicio : null;
            HostTeste::create([
                'host_id' => $host->id,
                'ip_destino' => $ip,
                'status_conexao' => 'falha',
                'latencia_ms' => null,
                'perda_pacotes' => 100,
                'ip_origem' => $this->obterIpOrigem(),
                'modo_execucao' => 'agendado',
                'executado_por' => 'scheduler',
                'resultado_json' => [
                    'erro' => $this->sanitizeOutput($e->getMessage()),
                    'saida' => $this->sanitizeOutput($e->getProcess()->getOutput()),
                    'saida_erro' => $this->sanitizeOutput($e->getProcess()->getErrorOutput()),
                    'comando_executado' => $comando,
                    'exit_code' => $e->getProcess()->getExitCode(),
                    'tempo_execucao' => $duracao,
                ],
            ]);
        }
    }

    private function extrairLatencia(string $saida): ?float
    {
        if (preg_match('/time[=<](\d+(?:\.\d+)?)/i', $saida, $matches)) {
            return (float) $matches[1];
        }

        if (preg_match('/tempo[=<](\d+(?:\.\d+)?)/i', $saida, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }

    private function extrairPerdaPacotes(string $saida, string $os): ?int
    {
        if ($os === 'windows') {
            if (preg_match('/(\d+)% de perda|(\d+)% loss/i', $saida, $matches)) {
                return (int) ($matches[1] ?? $matches[2]);
            }
        } else {
            if (preg_match('/(\d+)% packet loss/i', $saida, $matches)) {
                return (int) $matches[1];
            }
        }

        return null;
    }

    private function determinarStatusConexao(Process $process, ?int $perda): string
    {
        if (! $process->isSuccessful()) {
            return 'falha';
        }
        // Evita valores fora do ENUM da migration
        if ($perda !== null && $perda >= 75) {
            return 'falha';
        }

        return 'ativo';
    }

    private function obterIpOrigem(): ?string
    {
        try {
            $hostname = gethostname();
            if ($hostname) {
                $ip = gethostbyname($hostname);
                if ($ip && $ip !== $hostname) {
                    return $ip;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Não foi possível obter IP de origem: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Sanitiza saída de processos garantindo UTF-8 válido para JSON.
     */
    private function sanitizeOutput(?string $output): ?string
    {
        if ($output === null) {
            return null;
        }
        // Normaliza para UTF-8 e remove bytes inválidos
        try {
            $normalized = mb_convert_encoding($output, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
            $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $normalized);

            // Limita tamanho para evitar campos enormes
            return $clean !== false ? substr($clean, 0, 5000) : substr($output, 0, 5000);
        } catch (\Throwable $e) {
            // Fallback: corta saída crua se conversão falhar
            return substr($output, 0, 5000);
        }
    }
}
