<?php

namespace App\Listeners;

use App\Events\EmpenhoRegistrado;
use App\Models\SolicitacaoEmpenho;
use App\Services\AuditoriaService;

class MarcarSolicitacaoComoEmpenhada
{
    public function __construct(
        protected AuditoriaService $auditoria
    ) {}

    public function handle(EmpenhoRegistrado $event): void
    {
        if ($event->solicitacaoEmpenhoId) {
            $sol = SolicitacaoEmpenho::find($event->solicitacaoEmpenhoId);
            if ($sol) {
                $sol->update(['status' => 'empenhado']);
                $this->auditoria->record(
                    userId: $event->userId,
                    action: 'financeiro.solicitacao_empenho_empenhada',
                    registroId: $sol->id,
                    modulo: 'financeiro',
                    dados: ['empenho_id' => $event->empenhoId, 'contrato_id' => $event->contratoId]
                );
            }
        }
    }
}
