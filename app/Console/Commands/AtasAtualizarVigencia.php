<?php

namespace App\Console\Commands;

use App\Models\AtaRegistroPreco;
use Illuminate\Console\Command;

class AtasAtualizarVigencia extends Command
{
    protected $signature = 'atas:atualizar-vigencia';

    protected $description = 'Atualiza situação de vigência das atas de registro de preços';

    public function handle(): int
    {
        AtaRegistroPreco::query()->chunk(200, function ($atas) {
            foreach ($atas as $ata) {
                $ata->updateSituacaoAutomatic();
            }
        });

        return 0;
    }
}
