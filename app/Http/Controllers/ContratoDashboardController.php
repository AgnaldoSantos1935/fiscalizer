<?php

namespace App\Http\Controllers;

use App\Models\Contrato;

class ContratoDashboardController extends Controller
{
    public function index()
    {
        $total = Contrato::count();
        $ativos = Contrato::where('status', 'Ativo')->count();
        $encerrados = Contrato::where('status', 'Encerrado')->count();
        $suspensos = Contrato::where('status', 'Suspenso')->count();

        $porModalidade = Contrato::selectRaw('modalidade, COUNT(*) as total')
            ->groupBy('modalidade')
            ->get();

        // risco: baseado no campo riscos_detectados (json)
        $comRisco = Contrato::whereNotNull('riscos_detectados')->get();
        $altoRisco = $comRisco->filter(function ($c) {
            $riscos = json_decode($c->riscos_detectados, true) ?? [];
            return collect($riscos)->contains(fn($r) => ($r['impacto'] ?? '') === 'alto');
        })->count();

        return view('dashboard.contratos', compact(
            'total',
            'ativos',
            'encerrados',
            'suspensos',
            'porModalidade',
            'altoRisco'
        ));
    }
}
