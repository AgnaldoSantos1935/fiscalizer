<?php

namespace App\Http\Controllers;

use App\Models\BoletimMedicao;
use App\Models\Contrato;
use App\Models\Documento;
use App\Models\Medicao;
use App\Models\ProcessoLog;
use App\Models\Projeto;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Painel inicial do Fiscalizer
     */
    public function index()
    {
        // 🔹 Totais e indicadores filtrados pelos contratos do usuário
        if (Auth::check()) {
            $usuario = Auth::user();

            // IDs de contratos vinculados ao usuário
            $contratosQuery = Contrato::query()->doUsuario($usuario);
            $contratoIds = $contratosQuery->pluck('id');

            // Totais consolidados
            $totalContratos = $contratosQuery->count();
            $totalProjetos = Projeto::whereIn('contrato_id', $contratoIds)->count();
            $totalMedicoes = Medicao::whereIn('contrato_id', $contratoIds)->count();
            $totalBoletins = BoletimMedicao::whereHas('medicao', function ($q) use ($contratoIds) {
                $q->whereIn('contrato_id', $contratoIds);
            })->count();

            // Indicadores técnicos e financeiros
            $totalPF = BoletimMedicao::whereHas('medicao', function ($q) use ($contratoIds) {
                $q->whereIn('contrato_id', $contratoIds);
            })->sum('total_pf');
            $totalUST = BoletimMedicao::whereHas('medicao', function ($q) use ($contratoIds) {
                $q->whereIn('contrato_id', $contratoIds);
            })->sum('total_ust');
            $valorTotal = BoletimMedicao::whereHas('medicao', function ($q) use ($contratoIds) {
                $q->whereIn('contrato_id', $contratoIds);
            })->sum('valor_total');

            // Listas resumidas para modais (últimos 10)
            // Seleção resiliente: algumas bases podem não ter todas as colunas
            $selectContratos = ['id', 'numero'];
            if (Schema::hasColumn('contratos', 'situacao')) {
                $selectContratos[] = 'situacao';
            }
            if (Schema::hasColumn('contratos', 'tipo')) {
                $selectContratos[] = 'tipo';
            }

            $contratosResumo = Contrato::query()
                ->doUsuario($usuario)
                ->orderByDesc('id')
                ->take(10)
                ->get($selectContratos);

            $selectProjetos = ['id'];
            foreach (['codigo', 'titulo', 'situacao', 'status', 'contrato_id'] as $col) {
                if (Schema::hasColumn('projetos', $col)) {
                    $selectProjetos[] = $col;
                }
            }

            $projetosResumo = Projeto::query()
                ->whereIn('contrato_id', $contratoIds)
                ->orderByDesc('id')
                ->take(10)
                ->get($selectProjetos);

            $selectMedicoes = ['id'];
            foreach (['competencia', 'status', 'valor_liquido', 'contrato_id'] as $col) {
                if (Schema::hasColumn('medicoes', $col)) {
                    $selectMedicoes[] = $col;
                }
            }

            $medicoesResumo = Medicao::query()
                ->whereIn('contrato_id', $contratoIds)
                ->orderByDesc('id')
                ->take(10)
                ->get($selectMedicoes);

            // Top 5 projetos mais produtivos dos contratos do usuário
            $topProjetos = BoletimMedicao::select(
                'projetos.titulo as projeto',
                DB::raw('SUM(boletins_medicao.total_pf) as total_pf'),
                DB::raw('SUM(boletins_medicao.total_ust) as total_ust'),
                DB::raw('SUM(boletins_medicao.valor_total) as valor_total')
            )
                ->join('projetos', 'projetos.id', '=', 'boletins_medicao.projeto_id')
                ->join('medicoes', 'medicoes.id', '=', 'boletins_medicao.medicao_id')
                ->whereIn('medicoes.contrato_id', $contratoIds)
                ->groupBy('projetos.titulo')
                ->orderByDesc('total_pf')
                ->limit(5)
                ->get();
        } else {
            // Usuário não autenticado: visão zerada
            $totalContratos = 0;
            $totalProjetos = 0;
            $totalMedicoes = 0;
            $totalBoletins = 0;
            $totalPF = 0;
            $totalUST = 0;
            $valorTotal = 0;
            $topProjetos = collect();
            $contratosResumo = collect();
            $projetosResumo = collect();
            $medicoesResumo = collect();
        }

        // 🔹 Últimos boletins emitidos (apenas de contratos vinculados ao usuário)
        if (Auth::check()) {
            $usuario = Auth::user();
            $boletinsRecentes = BoletimMedicao::with(['projeto', 'medicao.contrato'])
                ->whereHas('medicao.contrato', function ($q) use ($usuario) {
                    $q->doUsuario($usuario);
                })
                ->latest()
                ->take(5)
                ->get();
        } else {
            $boletinsRecentes = collect();
        }

        // 🔹 Usuário logado e notificações
        $usuario = Auth::user();
        $notificacoesNaoLidas = 0;
        $ultimasNotificacoes = collect();
        if ($usuario) {
            $notificacoesNaoLidas = UserNotification::where('user_id', $usuario->id)
                ->where('lida', false)
                ->count();

            $ultimasNotificacoes = UserNotification::where('user_id', $usuario->id)
                ->latest()
                ->take(5)
                ->get();
        }

        $normasSugestoes = [];
        try {
            $rag = app(\App\Services\NormasRagService::class);
            $normasSugestoes = $rag->justificar('Definir próximos passos de contratação de equipamentos e serviços de tecnologia educacional em conformidade com normas técnicas e legais', [
                'idioma' => 'pt-BR'
            ]);
        } catch (\Throwable $e) {
            $normasSugestoes = [];
        }

        return view('dashboard.home', compact(
            'totalContratos',
            'totalProjetos',
            'totalMedicoes',
            'totalBoletins',
            'totalPF',
            'totalUST',
            'valorTotal',
            'topProjetos',
            'contratosResumo',
            'projetosResumo',
            'medicoesResumo',
            'boletinsRecentes',
            'usuario',
            'notificacoesNaoLidas',
            'ultimasNotificacoes',
            'normasSugestoes'
        ));
    }

    public function desempenhoFiscalAdm()
    {
        $user = auth()->user->id;

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
