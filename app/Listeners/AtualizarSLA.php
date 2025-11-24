<?php

namespace App\Listeners;

use App\Events\MedicaoHomologada;
use App\Events\PagamentoAutorizado;
use App\Events\PagamentoEfetuado;
use App\Services\AuditoriaService;
use App\Services\MedicaoService;

class AtualizarSLA
{
    public function __construct(
        protected MedicaoService $medicoes,
        protected AuditoriaService $auditoria,
    ) {}

    public function handle(MedicaoHomologada|PagamentoAutorizado|PagamentoEfetuado $event): void
    {
        if ($event instanceof MedicaoHomologada) {
            $this->medicoes->atualizarSLAHomologacao($event->medicaoId, $event->dados);
            $this->auditoria->record($event->userId, 'medicoes.sla_atualizado', $event->medicaoId, 'medicoes', $event->dados);

            return;
        }
        if ($event instanceof PagamentoAutorizado) {
            $this->medicoes->atualizarSLAPagamento($event->pagamentoId, $event->dados);
            $this->auditoria->record($event->userId, 'financeiro.sla_atualizado', $event->pagamentoId, 'financeiro', $event->dados);

            return;
        }
        if ($event instanceof PagamentoEfetuado) {
            $this->medicoes->atualizarSLAPagamento($event->pagamentoId, $event->dados);
            $this->auditoria->record($event->userId, 'financeiro.sla_atualizado', $event->pagamentoId, 'financeiro', $event->dados);

            return;
        }
    }
}
