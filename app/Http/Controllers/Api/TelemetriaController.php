<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgenteTelemetria;
use App\Models\Unidade;
use Illuminate\Http\Request;

class TelemetriaController extends Controller
{
    public function registrar(Request $r)
    {
        $data = $r->validate([
            'agent_key' => 'required',
            'unidade_token' => 'required',
            'agent_version' => 'required',
            'agent_uptime' => 'required',
            'system_uptime' => 'required',
            'cpu_usage' => 'required',
            'ram_used' => 'required',
            'internet_status' => 'required',
            'latency_ms' => 'nullable',
            'last_error' => 'nullable',
        ]);

        $unidade = Unidade::where('inventario_token', $data['unidade_token'])->firstOrFail();

        AgenteTelemetria::create(array_merge($data, [
            'unidade_id' => $unidade->id
        ]));

        return ['status' => 'ok'];
    }
}
