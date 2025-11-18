<?php

namespace App\Http\Controllers;

use App\Models\Medicao;

class MedicaoTelcoController extends Controller
{
    public function mapa(Medicao $medicao)
    {
        $medicao->load('itensTelco.escola');

        $pontos = $medicao->itensTelco->map(function ($item) {
            if (! $item->escola) {
                return null;
            }

            return [
                'nome' => $item->escola->nome,
                'lat' => $item->escola->latitude,
                'lng' => $item->escola->longitude,
                'uptime' => $item->uptime_percent,
                'downtime' => $item->downtime_minutos,
                'qtd_quedas' => $item->qtd_quedas,
            ];
        })->filter()->values();

        return view('medicoes.telco.mapa', [
            'medicao' => $medicao,
            'pontos' => $pontos,
        ]);
    }
}
