<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Host;
use App\Models\Projeto;
use App\Models\BoletimMedicao;
use App\Models\Medicao;
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
            'projetos.nome as projeto',
            DB::raw('SUM(boletins_medicao.total_pf) as total_pf'),
            DB::raw('SUM(boletins_medicao.total_ust) as total_ust'),
            DB::raw('SUM(boletins_medicao.valor_total) as valor_total')
        )
            ->join('projetos', 'projetos.id', '=', 'boletins_medicao.projeto_id')
            ->groupBy('projetos.nome')
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

        $stats = [
            'docs_enviados' => $user->documentos()->count(),
            'medicoes_finalizadas' => $user->medicoes()->where('status', 'concluida')->count(),
            'inconsistencias' => $user->logs()->where('acao', 'inconsistencia')->count(),
            'tempo_medio' => 18.6,
        ];

        return view('dashboard.fiscal_adm', compact('stats'));
    }
}
