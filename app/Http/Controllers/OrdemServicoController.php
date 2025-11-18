<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\OrdemServico;
use App\Services\OrdemServicoPdfService;

class OrdemServicoController extends Controller
{
    public function emitir(Demanda $demanda, OrdemServicoPdfService $pdfService)
    {
        $doc = $demanda->documentosTecnicos()->latest()->firstOrFail();

        if ($doc->status_validacao !== 'valido') {
            return back()->with('error', 'Documento técnico ainda não está validado.');
        }

        // Gera OS
        $os = $pdfService->gerarParaDemanda($demanda, $doc);

        // Gera hash da assinatura
        $hash = hash('sha256', json_encode([
            'os_id' => $os->id,
            'numero' => $os->numero_os,
            'pf' => $os->pf_total,
            'ust' => $os->ust_total,
            'data' => $os->data_emissao,
        ]));

        $os->assinatura_hash = $hash;
        $os->verificacao_url = route('os.verificar', $os->id);
        $os->save();

        return redirect()->route('ordens_servico.show', $os->id)
            ->with('success', 'Ordem de Serviço emitida e assinada digitalmente.');
    }

    public function verificar(OrdemServico $os)
    {
        return view('ordens_servico.verificar', compact('os'));
    }
}
