<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Projeto;

class ProjetoApiController extends Controller
{
    public function apf(Projeto $projeto)
    {
        $apfs = $projeto->apfs()
            ->select('id', 'total_pf', 'observacao', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $apfs]);
    }

    public function atividades(Projeto $projeto)
    {
        $atividades = $projeto->atividades()
            ->select('id', 'data', 'etapa', 'analista', 'horas', 'descricao')
            ->orderByDesc('data')
            ->get();

        return response()->json(['data' => $atividades]);
    }

    public function medicao(Projeto $projeto)
    {
        $itens = $projeto->itensMedicao()
            ->select('id', 'descricao', 'quantidade', 'valor_unitario')
            ->get()
            ->map(function ($item) {
                $item->total = $item->quantidade * $item->valor_unitario;

                return $item;
            });

        return response()->json(['data' => $itens]);
    }

    public function boletins(Projeto $projeto)
    {
        $boletins = $projeto->boletins()
            ->select('id', 'total_pf', 'total_ust', 'valor_total', 'data_emissao')
            ->orderByDesc('data_emissao')
            ->get();

        return response()->json(['data' => $boletins]);
    }

    /* ==== APIs extras para Dashboard (itens 2 e 5) ==== */

    // Série PF/UST por boletim
    public function dashboardPfUst(Projeto $projeto)
    {
        $boletins = $projeto->boletins()
            ->orderBy('data_emissao')
            ->get(['data_emissao', 'total_pf', 'total_ust']);

        return response()->json([
            'labels' => $boletins->pluck('data_emissao')->map(fn ($d) => $d?->format('m/Y')),
            'pf' => $boletins->pluck('total_pf'),
            'ust' => $boletins->pluck('total_ust'),
        ]);
    }

    // Esforço por mês (soma de horas das atividades)
    public function dashboardEsforco(Projeto $projeto)
    {
        $atividades = $projeto->atividades()
            ->whereNotNull('data')
            ->get(['data', 'horas']);

        $grouped = $atividades->groupBy(fn ($a) => $a->data->format('Y-m'));

        $labels = [];
        $horas = [];

        foreach ($grouped as $periodo => $lista) {
            $labels[] = \Carbon\Carbon::createFromFormat('Y-m', $periodo)->format('m/Y');
            $horas[] = $lista->sum('horas');
        }

        return response()->json(compact('labels', 'horas'));
    }
}
