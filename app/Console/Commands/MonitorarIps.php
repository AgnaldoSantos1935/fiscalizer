<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonitoramentoIp;
use App\Services\MonitoramentoIpService;

class MonitorarIps extends Command
{
    protected $signature = 'monitorar:ips';
    protected $description = 'Verifica o status dos IPs cadastrados';

    public function handle()
    {
        $this->info('Iniciando verificação dos IPs...');
        foreach (MonitoramentoIp::where('ativo', true)->get() as $host) {
            MonitoramentoIpService::testar($host);
            $status = $host->online ? 'ONLINE' : 'OFFLINE';
            $this->info("{$host->nome} ({$host->endereco_ip}) => {$status}");
        }
        $this->info('Verificação concluída.');
    }
}

