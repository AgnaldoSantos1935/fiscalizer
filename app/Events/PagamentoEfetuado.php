<?php

namespace App\Events;

class PagamentoEfetuado
{
    public function __construct(
        public int $pagamentoId,
        public int $empenhoId,
        public int $contratoId,
        public int $userId,
        public array $dados = []
    ) {}
}
