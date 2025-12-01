<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipamento;
use App\Models\Unidade;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function registrar(Request $request)
    {
        $data = $request->validate([
            'agent_key' => 'required|string',
            'unidade_token' => 'required|string',
            'hostname' => 'required|string',
            'serial' => 'required|string',
            'cpu' => 'nullable|string',
            'ram_gb' => 'nullable|integer',
            'sistema_operacional' => 'nullable|string',
            'ip' => 'nullable|string',
            'discos' => 'nullable|array',
        ]);

        $unidade = Unidade::where('inventario_token', $data['unidade_token'])->firstOrFail();

        $equipamento = Equipamento::updateOrCreate(
            ['serial_number' => $data['serial']],
            [
                'unidade_id' => $unidade->id,
                'hostname' => $data['hostname'],
                'cpu_resumida' => $data['cpu'],
                'ram_gb' => $data['ram_gb'],
                'sistema_operacional' => $data['sistema_operacional'],
                'ip_atual' => $data['ip'],
                'discos' => $data['discos'],
                'origem_inventario' => 'agente_windows',
                'ultimo_checkin' => now(),
                'status' => 'ativo',
            ]
        );

        return ['status' => 'ok', 'equipamento_id' => $equipamento->id];
    }
}
