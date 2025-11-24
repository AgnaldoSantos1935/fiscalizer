<?php

namespace App\Http\Controllers;

use App\Models\Medicao;
use Illuminate\Support\Facades\Auth;

class AntifraudeDashboardController extends Controller
{
    public function index()
    {
        // últimas N medições com agregados (filtradas pelos contratos do usuário)
        if (Auth::check()) {
            $usuario = Auth::user();
            $medicoes = Medicao::with('itens', 'contrato.empresa')
                ->whereHas('contrato', function ($q) use ($usuario) {
                    $q->doUsuario($usuario);
                })
                ->latest('created_at')
                ->take(20)
                ->get();
        } else {
            $medicoes = collect();
        }

        $cards = [
            'total_medicoes' => $medicoes->count(),
            'total_pf' => $medicoes->sum(fn ($m) => $m->itens->sum('quantidade_pf')),
            'total_horas' => $medicoes->sum(fn ($m) => $m->itens->sum('horas_executadas')),
            'total_pessoas' => $medicoes->sum(fn ($m) => $m->itens->sum('qtd_pessoas')),
        ];

        // dados para gráfico: PF x horas por medição
        $chartData = $medicoes->map(function ($m) {
            $pf = $m->itens->sum('quantidade_pf');
            $horas = $m->itens->sum('horas_executadas');

            return [
                'id' => $m->id,
                'label' => "Medição #{$m->id}",
                'pf' => $pf,
                'horas' => $horas,
                'horas_por_pf' => $pf > 0 ? round($horas / $pf, 2) : 0,
            ];
        });

        // você pode também trazer top medições com horas_por_pf estranhas

        return view('dashboard.antifraude', compact('cards', 'chartData'));
    }
}
