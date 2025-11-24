<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PagamentoAutorizado
{
    use Dispatchable, SerializesModels;

    public int $pagamentoId;

    public int $contratoId;

    public ?int $userId;

    public array $dados;

    public function __construct(int $pagamentoId, int $contratoId, ?int $userId = null, array $dados = [])
    {
        $this->pagamentoId = $pagamentoId;
        $this->contratoId = $contratoId;
        $this->userId = $userId;
        $this->dados = $dados;
    }
}
