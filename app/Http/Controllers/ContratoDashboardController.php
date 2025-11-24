<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Support\Facades\Auth;

class ContratoDashboardController extends Controller
{
    public function index()
    {
        // Base filtrada por contratos vinculados ao usuário
        if (Auth::check()) {
            $usuario = Auth::user();
            $baseQuery = Contrato::query()->doUsuario($usuario);
        } else {
            $baseQuery = Contrato::query()->whereRaw('1 = 0');
        }

        $total = (clone $baseQuery)->count();
        $ativos = (clone $baseQuery)->where('status', 'Ativo')->count();
        $encerrados = (clone $baseQuery)->where('status', 'Encerrado')->count();
        $suspensos = (clone $baseQuery)->where('status', 'Suspenso')->count();

        $porModalidade = (clone $baseQuery)->selectRaw('modalidade, COUNT(*) as total')
            ->groupBy('modalidade')
            ->get();

        // risco: baseado no campo riscos_detectados (json)
        $comRisco = (clone $baseQuery)->whereNotNull('riscos_detectados')->get();
        $altoRisco = $comRisco->filter(function ($c) {
            $riscos = json_decode($c->riscos_detectados, true) ?? [];

            return collect($riscos)->contains(fn ($r) => ($r['impacto'] ?? '') === 'alto');
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
