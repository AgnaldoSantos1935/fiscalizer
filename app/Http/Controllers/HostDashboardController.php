<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\HostTeste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostDashboardController extends Controller
{
    // 🔹 Página principal do dashboard
    public function index()
    {
        $provedores = Host::select('provedor')
            ->distinct()
            ->pluck('provedor')
            ->filter()
            ->sort()
            ->values();

        return view('host_testes.dashboard', compact('provedores'));
    }

    // 🔹 Dados AJAX (gráficos principais)
    public function dadosAjax(Request $request)
    {
        $inicio = $request->input('inicio', now()->subDays(30)->toDateString());
        $fim = $request->input('fim', now()->toDateString());
        $provedor = $request->input('provedor');

        $query = HostTeste::whereBetween('created_at', [$inicio, $fim])
            ->when($provedor, function ($q) use ($provedor) {
                $q->whereHas('host', fn ($h) => $h->where('provedor', $provedor));
            });

        $total = $query->count();
        $ativos = $query->where('status_conexao', 'ativo')->count();
        $falhas = $total - $ativos;

        $latMedia = round($query->avg('latencia_ms') ?? 0, 2);
        $perdaMedia = round($query->avg('perda_pacotes') ?? 0, 2);

        // Distribuição de status
        $statusDist = HostTeste::select('status_conexao', DB::raw('COUNT(*) as total'))
            ->groupBy('status_conexao')
            ->pluck('total', 'status_conexao');

        // Latência média por provedor
        $latPorProv = DB::table('host_testes as ht')
            ->join('hosts as h', 'ht.host_id', '=', 'h.id')
            ->select('h.provedor', DB::raw('AVG(ht.latencia_ms) as media'))
            ->groupBy('h.provedor')
            ->get();

        // Perda média por provedor
        $perdaPorProv = DB::table('host_testes as ht')
            ->join('hosts as h', 'ht.host_id', '=', 'h.id')
            ->select('h.provedor', DB::raw('AVG(ht.perda_pacotes) as media'))
            ->groupBy('h.provedor')
            ->get();

        // Top 10 mais lentos
        $topLentos = DB::table('host_testes as ht')
            ->join('hosts as h', 'ht.host_id', '=', 'h.id')
            ->select('h.id', 'h.nome_conexao', DB::raw('AVG(ht.latencia_ms) as media'))
            ->groupBy('h.id', 'h.nome_conexao')
            ->orderByDesc('media')
            ->limit(10)
            ->get();

        return response()->json([
            'total' => $total,
            'ativos' => $ativos,
            'falhas' => $falhas,
            'latencia' => $latMedia,
            'perda' => $perdaMedia,
            'statusDist' => $statusDist,
            'latPorProv' => $latPorProv,
            'perdaPorProv' => $perdaPorProv,
            'topLentos' => $topLentos,
        ]);
    }

    // 🔹 Histórico técnico por host (para o modal)
    public function historicoHost($id)
    {
        $testes = HostTeste::where('host_id', $id)
            ->orderBy('created_at', 'asc')
            ->selectRaw('DATE(created_at) as data,
                         AVG(latencia_ms) as latencia_media,
                         AVG(perda_pacotes) as perda_media,
                         SUM(CASE WHEN status_conexao = "ativo" THEN 1 ELSE 0 END) / COUNT(*) * 100 as uptime_percent')
            ->groupBy('data')
            ->get();

        return response()->json($testes);
    }

    public function historicoAjax(Request $request)
    {
        if ($request->ajax()) {
            $inicio = $request->input('inicio', now()->subDays(30)->toDateString());
            $fim = $request->input('fim', now()->toDateString());
            $provedor = $request->input('provedor');
            $hostId = $request->input('host_id');

            $query = HostTeste::with('host')
                ->whereBetween('created_at', [$inicio, $fim])
                ->when($provedor, fn ($q) => $q->whereHas('host', fn ($h) => $h->where('provedor', 'like', "%$provedor%"))
                )
                ->when($hostId, fn ($q) => $q->where('host_id', $hostId))
                ->orderBy('created_at', 'desc');

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('nome_conexao', fn ($row) => $row->host->nome_conexao ?? '—')
                ->addColumn('provedor', fn ($row) => $row->host->provedor ?? '—')
                ->addColumn('status_conexao', function ($row) {
                    $status = strtolower($row->status_conexao);
                    $badge = match ($status) {
                        'ativo' => 'success',
                        'falha' => 'danger',
                        default => 'secondary'
                    };

                    return "<span class='badge bg-$badge text-uppercase px-3 py-2'>
                            <i class='fas fa-circle me-1'></i>$status
                        </span>";
                })
                ->addColumn('latencia_ms', fn ($row) => $row->latencia_ms ? number_format($row->latencia_ms, 2, ',', '.') . ' ms' : '—')
                ->addColumn('perda_pacotes', fn ($row) => $row->perda_pacotes ? number_format($row->perda_pacotes, 1, ',', '.') . '%' : '—')
                ->addColumn('modo_execucao', fn ($row) => ucfirst($row->modo_execucao))
                ->addColumn('executado_por', fn ($row) => $row->executado_por ?? '—')
                ->addColumn('created_at', fn ($row) => $row->created_at->format('d/m/Y H:i'))
                ->rawColumns(['status_conexao'])
                ->make(true);
        }

        // Filtros para o select
        $hosts = Host::orderBy('nome_conexao')->get();
        $provedores = $hosts->pluck('provedor')->unique()->filter();

        return view('host_testes.historico', compact('hosts', 'provedores'));
    }
}
