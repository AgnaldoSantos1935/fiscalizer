<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Host;

class HostApiController extends Controller
{
    public function index()
    {
        $hosts = Host::select(
                'id',
                'nome_conexao',
                'provedor',
                'tecnologia',
                'tipo_monitoramento',
                'host_alvo',
                'porta',
                'status'
            )
            ->orderBy('nome_conexao')
            ->get();

        return response()->json([
            'data' => $hosts
        ]);
    }
}
