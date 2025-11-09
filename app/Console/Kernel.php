<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Register your custom commands here, e.g. \App\Console\Commands\MyCommand::class,
        \App\Console\Commands\TestarConectividadeHosts::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
protected function schedule(Schedule $schedule)
{
 $schedule->command('hosts:testar')->everyFiveMinutes();
    // Executa o job a cada 10 minutos
  //  $schedule->job(new \App\Jobs\MonitorarHostsJob)->everyTenMinutes();
//$schedule->command('medicao:gerar-boletins')->monthlyOn(1, '02:00');
    // 🔧 Pode ajustar conforme a carga:
    // $schedule->command('monitorar:rede')->hourly();
    // $schedule->command('monitorar:rede')->everyFiveMinutes();
}
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
