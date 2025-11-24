<?php

namespace App\Listeners;

use App\Events\MedicaoHomologada;
use App\Services\AuditoriaService;
use App\Services\FinanceiroService;

class CriarSolicitacaoPagamento
{
    public function __construct(
        protected FinanceiroService $financeiro,
        protected AuditoriaService $auditoria,
    ) {}

    public function handle(MedicaoHomologada $event): void
    {
        // Cria título financeiro pendente a partir da medição homologada
        $tituloId = $this->financeiro->criarTituloAPartirDaMedicao(
            $event->medicaoId,
            $event->contratoId,
            array_merge($event->dados, ['usuario_solicitante_id' => $event->userId])
        );
        // Auditoria
        $this->auditoria->record(
            userId: $event->userId,
            action: 'financeiro.titulo_criado',
            registroId: $tituloId,
            modulo: 'financeiro',
            dados: [
                'medicao_id' => $event->medicaoId,
                'contrato_id' => $event->contratoId,
            ]
        );
    }
}
