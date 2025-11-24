<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Support\Facades\Auth;

class ContratoConformidadeController extends Controller
{
    public function index()
    {
        // Filtrar por contratos vinculados ao usuário autenticado
        if (Auth::check()) {
            $usuario = Auth::user();
            $baseQuery = Contrato::query()->doUsuario($usuario);
        } else {
            // Usuário não autenticado: visão zerada
            $baseQuery = Contrato::query()->whereRaw('1 = 0');
        }

        $total = (clone $baseQuery)->count();
        $mediaScore = (clone $baseQuery)->whereNotNull('risco_score')->avg('risco_score');

        $porNivel = (clone $baseQuery)->selectRaw('risco_nivel, COUNT(*) as total')
            ->groupBy('risco_nivel')
            ->get();

        // Top 10 contratos mais críticos (menor score)
        $criticos = (clone $baseQuery)->whereNotNull('risco_score')
            ->orderBy('risco_score', 'asc')
            ->take(10)
            ->get();

        $totalCriticos = (clone $baseQuery)->where('risco_score', '<', 40)->count();

        return view('dashboard.conformidade_contratos', compact(
            'total',
            'mediaScore',
            'porNivel',
            'criticos',
            'totalCriticos'
        ));
    }
}
