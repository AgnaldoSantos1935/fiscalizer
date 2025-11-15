<?php

namespace App\Http\Controllers;

use App\Models\Contrato;

class ContratoConformidadeController extends Controller
{
    public function index()
    {
        $total = Contrato::count();
        $mediaScore = Contrato::whereNotNull('risco_score')->avg('risco_score');

        $porNivel = Contrato::selectRaw('risco_nivel, COUNT(*) as total')
            ->groupBy('risco_nivel')
            ->get();

        // Top 10 contratos mais críticos (menor score)
        $criticos = Contrato::whereNotNull('risco_score')
            ->orderBy('risco_score', 'asc')
            ->take(10)
            ->get();

        return view('dashboard.conformidade_contratos', compact(
            'total',
            'mediaScore',
            'porNivel',
            'criticos'
        ));
    }
}
