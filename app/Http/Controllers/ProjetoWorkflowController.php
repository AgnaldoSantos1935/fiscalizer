<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\ProcessoInstancia;
use App\Models\Projeto;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class ProjetoWorkflowController extends Controller
{
    public function __construct(
        protected WorkflowService $workflowService
    ) {}

    public function show($projetoId)
    {
        $projeto   = Projeto::findOrFail($projetoId);
        $instancia = $projeto->processoInstancia;

        return view('projetos.workflow.show', compact('projeto', 'instancia'));
    }

    public function iniciar($projetoId)
    {
        $projeto = Projeto::findOrFail($projetoId);

        if ($projeto->processoInstancia) {
            return redirect()
                ->route('projetos.workflow.show', $projeto->id)
                ->with('warning', 'Este projeto já possui um processo em execução.');
        }

        $processo = Processo::where('codigo', 'PROJ_DEV_SIST')->firstOrFail();

        $instancia = $this->workflowService->iniciarProcessoParaReferencia($processo, $projeto);

        return redirect()
            ->route('projetos.workflow.show', $projeto->id)
            ->with('success', 'Fluxo BPM do projeto iniciado com sucesso.');
    }

    public function avancar(Request $request, $projetoId)
    {
        $projeto   = Projeto::findOrFail($projetoId);
        $instancia = $projeto->processoInstancia;

        if (! $instancia) {
            return redirect()
                ->route('projetos.workflow.show', $projeto->id)
                ->with('error', 'Projeto não possui processo iniciado.');
        }

        $this->workflowService->avancar($instancia, $request->input('observacoes'));

        return redirect()
            ->route('projetos.workflow.show', $projeto->id)
            ->with('success', 'Etapa avançada com sucesso.');
    }
}
