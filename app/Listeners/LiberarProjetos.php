<?php

namespace App\Listeners;

use App\Events\ContratoAssinado;
use App\Services\AuditoriaService;
use App\Services\ContratoService;

class LiberarProjetos
{
    public function __construct(
        protected ContratoService $contratos,
        protected AuditoriaService $auditoria,
    ) {}

    public function handle(ContratoAssinado $event): void
    {
        $this->contratos->liberarProjetosParaContrato($event->contratoId);
        $this->auditoria->record($event->userId, 'contratos.projetos_liberados', $event->contratoId, 'contratos', $event->dados);
    }
}
