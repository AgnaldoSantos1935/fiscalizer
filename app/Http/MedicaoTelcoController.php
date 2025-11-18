<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\MedicaoItemTelco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MedicaoTelcoController extends Controller
{
    public function criarParaContrato(Contrato $contrato)
    {
        return view('medicoes.telco.create', compact('contrato'));
    }

    public function store(Request $request, Contrato $contrato)
    {
        $dados = $request->validate([
            'competencia' => 'required',
            'valor_mensal_contratado' => 'required|numeric',
        ]);

        // 1) chama backend Python pra buscar SLA
        $backendUrl = config('services.monitor_backend.url'); // ex: http://monitor:8002

        $response = Http::post($backendUrl.'/telco/sla', [
            'agent_id' => $contrato->agent_id_telco,
            'host_label' => null, // ou um host específico
        ]);

        if (! $response->successful()) {
            return back()->with('error', 'Não foi possível obter SLA do backend de monitoramento.');
        }

        $slaData = $response->json(); // uptime_percent, etc.

        $uptime = $slaData['uptime_percent'];

        // 2) calcula penalidade simples (exemplo)
        $slaContratado = 99.0;
        $desconto = 0;

        if ($uptime < $slaContratado) {
            $delta = $slaContratado - $uptime;
            // regra simples: 1% de desconto a cada 1% abaixo do SLA
            $desconto = ($delta / 100.0) * $dados['valor_mensal_contratado'];
        }

        $valorFinal = $dados['valor_mensal_contratado'] - $desconto;

        // 3) cria medição
        $medicao = Medicao::create([
            'contrato_id' => $contrato->id,
            'competencia' => $dados['competencia'],
            'tipo' => 'telco',
            'valor_bruto' => $dados['valor_mensal_contratado'],
            'valor_desconto' => $desconto,
            'valor_liquido' => $valorFinal,
            'sla_contratado' => $slaContratado,
            'sla_alcancado' => $uptime,
            'status' => 'rascunho',
            'resumo_json' => json_encode($slaData),
        ]);

        // 4) item resumido (por contrato, ou você pode abrir por escola/link)
        MedicaoItemTelco::create([
            'medicao_id' => $medicao->id,
            'escola_id' => null, // se tiver
            'localidade' => $contrato->empresa_razao_social ?? null,
            'link_id' => null,
            'uptime_percent' => $uptime,
            'downtime_minutos' => 0, // poderia vir do backend futuramente
            'qtd_quedas' => 0, // idem
            'valor_mensal_contratado' => $dados['valor_mensal_contratado'],
            'valor_desconto' => $desconto,
            'valor_final' => $valorFinal,
            'eventos_json' => null,
        ]);

        return redirect()->route('medicoes.show', $medicao->id)
            ->with('success', 'Medição telco criada com base no SLA real medido pelo backend.');
    }
}
