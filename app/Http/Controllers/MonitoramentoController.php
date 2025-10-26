<?php

namespace App\Http\Controllers;

use App\Models\Monitoramento;
use Illuminate\Http\Request;

class MonitoramentoController extends Controller
{
    // Tela principal com tabela de status
    public function index()
    {
        $itens = Monitoramento::orderBy('nome')->get();
        return view('monitoramentos.index', compact('itens'));
    }

    // Dashboard de SLA, uptime e latência média
    public function dashboard()
    {
        // Carrega os logs de cada item
        $itens = Monitoramento::with('logs')->get();

        // Calcula métricas de disponibilidade e latência
        $estatisticas = $itens->map(function ($item) {
            $total = $item->logs->count();
            $online = $item->logs->where('online', true)->count();
            $uptime = $total > 0 ? round(($online / $total) * 100, 2) : 0;
            $latenciaMedia = round($item->logs->whereNotNull('latencia')->avg('latencia'), 2);

            return [
                'id' => $item->id,
                'nome' => $item->nome,
                'tipo' => $item->tipo,
                'alvo' => $item->alvo,
                'uptime' => $uptime,
                'latencia' => $latenciaMedia,
                'total_logs' => $total,
            ];
        });

        return view('monitoramentos.dashboard', compact('estatisticas'));
    }
}
