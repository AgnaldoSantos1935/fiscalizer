<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\TestarConectividadeHosts;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// 🔹 Comando manual para testar todas as conexões
Artisan::command('hosts:testar', function () {
    $this->call(TestarConectividadeHosts::class);
})->purpose('Executa testes de conectividade em todos os hosts ativos.');
