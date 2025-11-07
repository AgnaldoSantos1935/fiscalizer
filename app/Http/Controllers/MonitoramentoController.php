<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\MonitoramentoHost;

class MonitoramentoController extends Controller
{
    public function index()
    {
        $ultimos = MonitoramentoHost::with('host')
            ->latest('verificado_em')
            ->take(100)
            ->get();

        $resumo = [
            'total' => Host::count(),
            'online' => Host::where('status', 'online')->count(),
            'offline' => Host::where('status', 'offline')->count(),
        ];

        return view('monitoramentos.conexoes', compact('ultimos', 'resumo'));
    }
}

