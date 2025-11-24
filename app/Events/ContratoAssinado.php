<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContratoAssinado
{
    use Dispatchable, SerializesModels;

    public int $contratoId;

    public ?int $userId;

    public array $dados;

    public function __construct(int $contratoId, ?int $userId = null, array $dados = [])
    {
        $this->contratoId = $contratoId;
        $this->userId = $userId;
        $this->dados = $dados;
    }
}
