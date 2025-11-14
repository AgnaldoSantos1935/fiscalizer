<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\Indisponibilidade;
use App\Models\PDF;
use App\Models\Excel;
use App\Exports\Api\NocMapController;

class NocReportController extends Controller
{
    public function pdf()
    {
        $hosts = Host::with('monitoramentos')->get();
        $html = view('noc.reports.pdf', compact('hosts'))->render();

        return PDF::loadHTML($html)->download('relatorio_noc.pdf');
    }

    public function excel()
    {
        return Excel::download(new NocExport, 'relatorio_noc.xlsx');
    }
    public function mapaSla()
{
    // últimos 30 dias
    $inicio = now()->subDays(30);

    $hosts = Host::with(['itemContrato', 'escola', 'monitoramentos' => function($q) use ($inicio){
        $q->where('ultima_verificacao', '>=', $inicio);
    }])->get();

    $result = $hosts->map(function($h){

        $total = $h->monitoramentos->count();
        $online = $h->monitoramentos->where('online', 1)->count();

        $sla_real = $total > 0 ? round(($online / $total) * 100, 2) : null;
        $sla_minimo = $h->itemContrato->sla_minimo ?? 98;

        return [
            'id'       => $h->id,
            'nome'     => $h->nome_conexao,
            'escola'   => $h->escola->nome ?? null,
            'lat'      => $h->escola->latitude ?? null,
            'lng'      => $h->escola->longitude ?? null,
            'sla_real' => $sla_real,
            'sla_min'  => $sla_minimo,
        ];
    });

    return response()->json($result);
}
public function topDowntime()
{
    // últimos 30 dias
    $inicio = now()->subDays(30);

    $hosts = Host::with(['monitoramentos' => function($q) use ($inicio){
        $q->where('ultima_verificacao', '>=', $inicio);
    }])->get();

    $result = $hosts->map(function($h){
        $off = $h->monitoramentos->where('online', 0)->count();
        $tot = $h->monitoramentos->count();
        $downtime = $off * 5; // 5 minutos entre monitoramentos (ajuste conforme intervalo real)

        return [
            'host' => $h->nome_conexao,
            'downtime_min' => $downtime,
            'quedas' => $off
        ];
    })
    ->sortByDesc('downtime_min')
    ->take(10)
    ->values();

    return response()->json($result);
}

}
