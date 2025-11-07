<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FiscalizerMonitorarHosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fiscalizer-monitorar-hosts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         dispatch(new \App\Jobs\MonitorarHostsJob());
    $this->info('✅ Monitoramento de hosts disparado!');
    }
}
