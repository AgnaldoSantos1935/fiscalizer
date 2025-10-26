<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Monitoramento;
use App\Services\MonitoramentoService;

class MonitorarConexoes extends Command
{
    protected $signature = 'monitorar:conexoes';
    protected $description = 'Executa o monitoramento de IPs e Links cadastrados';

    public function handle()
    {
        $this->info('Iniciando verificação...');
        $itens = Monitoramento::where('ativo', true)->get();

        foreach ($itens as $item) {
            MonitoramentoService::testar($item);
            $status = $item->online ? 'ONLINE' : 'OFFLINE';
            $this->info("{$item->nome} ({$item->alvo}) => {$status}");
        }

        $this->info('Monitoramento concluído.');
    }
}
