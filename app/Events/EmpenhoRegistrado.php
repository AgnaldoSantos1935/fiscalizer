<?php

namespace App\Events;

class EmpenhoRegistrado
{
    public function __construct(
        public int $empenhoId,
        public ?int $solicitacaoEmpenhoId,
        public int $contratoId,
        public int $userId,
        public array $dados = []
    ) {}
}
