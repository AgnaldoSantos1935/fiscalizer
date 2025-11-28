<?php

namespace App\Http\Controllers;

use App\Models\BoletimMedicao;
use App\Models\Contrato;
use App\Models\Documento;
use App\Models\Medicao;
use App\Models\ProcessoLog;
use App\Models\Projeto;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Painel inicial do Fiscalizer
     */
    public function index()
    {
        // 🔹 Totais gerais
        $totalContratos = Contrato::count();
        $totalProjetos = Projeto::count();
        $totalMedicoes = Medicao::count();
        $totalBoletins = BoletimMedicao::count();

        // 🔹 Indicadores técnicos e financeiros
        $totalPF = BoletimMedicao::sum('total_pf');
        $totalUST = BoletimMedicao::sum('total_ust');
        $valorTotal = BoletimMedicao::sum('valor_total');

        // 🔹 Top 5 projetos mais produtivos
        $topProjetos = BoletimMedicao::select(
            'projetos.titulo as projeto',
            DB::raw('SUM(boletins_medicao.total_pf) as total_pf'),
            DB::raw('SUM(boletins_medicao.total_ust) as total_ust'),
            DB::raw('SUM(boletins_medicao.valor_total) as valor_total')
        )
            ->join('projetos', 'projetos.id', '=', 'boletins_medicao.projeto_id')
            ->groupBy('projetos.titulo')
            ->orderByDesc('total_pf')
            ->limit(5)
            ->get();

        // 🔹 Últimos boletins emitidos
        $boletinsRecentes = BoletimMedicao::with(['projeto', 'medicao.contrato'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.home', compact(
            'totalContratos',
            'totalProjetos',
            'totalMedicoes',
            'totalBoletins',
            'totalPF',
            'totalUST',
            'valorTotal',
            'topProjetos',
            'boletinsRecentes'
        ));
    }

    public function desempenhoFiscalAdm()
    {
        $user = auth()->user();

        // Documentos enviados pelo usuário (campo created_by em Documento)
        $docsEnviados = Documento::where('created_by', $user->id)->count();

        // Medições finalizadas com participação do usuário (via logs vinculados à medição)
        // Assume relação Medicao::logs() existente conforme uso em serviços de validação
        $medicoesFinalizadas = Medicao::where('status', 'concluida')
            ->whereHas('logs', function ($q) use ($user) {
                $q->where('usuario_id', $user->id);
            })
            ->count();

        // Inconsistências registradas pelo usuário (ProcessoLog com acao = 'inconsistencia')
        $inconsistencias = ProcessoLog::where('usuario_id', $user->id)
            ->where('acao', 'inconsistencia')
            ->count();

        $stats = [
            'docs_enviados' => $docsEnviados,
            'medicoes_finalizadas' => $medicoesFinalizadas,
            'inconsistencias' => $inconsistencias,
            'tempo_medio' => 18.6,
        ];

        return view('dashboard.fiscal_adm', compact('stats'));
    }
}
