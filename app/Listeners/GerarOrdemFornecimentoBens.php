<?php

namespace App\Listeners;

use App\Events\EmpenhoRegistrado;
use App\Models\Contrato;
use App\Models\Empenho;
use App\Services\AuditoriaService;
use App\Services\OrdemFornecimentoPdfService;

class GerarOrdemFornecimentoBens
{
    public function __construct(
        protected OrdemFornecimentoPdfService $ofService,
        protected AuditoriaService $auditoria,
    ) {}

    public function handle(EmpenhoRegistrado $event): void
    {
        $contrato = Contrato::find($event->contratoId);
        $empenho = Empenho::with('itens')->find($event->empenhoId);

        if (! $contrato || ! $empenho) {
            return;
        }

        $tipo = strtolower((string) ($contrato->tipo ?? ''));
        $tiposMateriais = ['material', 'bens', 'bem', 'fornecimento'];
        if (! in_array($tipo, $tiposMateriais, true)) {
            return; // Somente para contratos de bens/materiais
        }

        try {
            $of = $this->ofService->gerarParaEmpenho($contrato, $empenho);
            $this->auditoria->record(
                userId: $event->userId,
                action: 'financeiro.ordem_fornecimento_gerada',
                registroId: $of->id,
                modulo: 'financeiro',
                dados: [
                    'empenho_id' => $event->empenhoId,
                    'contrato_id' => $event->contratoId,
                    'ordem_fornecimento_id' => $of->id,
                    'arquivo_pdf' => $of->arquivo_pdf,
                ]
            );
        } catch (\Throwable $e) {
            // Silencia falhas para não quebrar fluxo de registro do empenho
        }
    }
}
