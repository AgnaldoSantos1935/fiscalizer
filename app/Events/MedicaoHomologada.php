<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicaoHomologada
{
    use Dispatchable, SerializesModels;

    public int $medicaoId;

    public int $contratoId;

    public ?int $userId;

    public array $dados;

    public function __construct(int $medicaoId, int $contratoId, ?int $userId = null, array $dados = [])
    {
        $this->medicaoId = $medicaoId;
        $this->contratoId = $contratoId;
        $this->userId = $userId;
        $this->dados = $dados;
    }
}
