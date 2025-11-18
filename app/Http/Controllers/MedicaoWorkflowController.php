<?php

namespace App\Http\Controllers;

use App\Models\Medicao;
use App\Models\Processo;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class MedicaoWorkflowController extends Controller
{
    public function __construct(
        protected WorkflowService $workflow
    ) {}

    public function show($medicaoId)
    {
        $medicao = Medicao::findOrFail($medicaoId);
        $instancia = $medicao->processoInstancia;

        return view('medicoes.workflow.show', compact('medicao', 'instancia'));
    }

    public function iniciar($medicaoId)
    {
        $medicao = Medicao::findOrFail($medicaoId);
        $processo = Processo::where('codigo', 'MEDICAO_SERVICOS')->firstOrFail();

        $this->workflow->iniciarProcessoParaReferencia($processo, $medicao);

        return back()->with('success', 'Fluxo de medição iniciado.');
    }

    public function avancar(Request $request, $medicaoId)
    {
        $medicao = Medicao::findOrFail($medicaoId);
        $instancia = $medicao->processoInstancia;

        $this->workflow->avancar($instancia, $request->observacoes);

        return back()->with('success', 'Etapa avançada com sucesso.');
    }
}
