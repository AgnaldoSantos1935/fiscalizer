<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\MedicaoItemFixoMensal;
use App\Models\MedicaoItemSoftware;
use App\Models\MedicaoItemTelco;
use Illuminate\Http\Request;

class MedicaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicao::with('contrato');

        if ($contratoTxt = trim((string) $request->get('contrato'))) {
            $query->whereHas('contrato', function ($q) use ($contratoTxt) {
                $q->where('numero', 'like', "%{$contratoTxt}%");
            });
        }
        if ($comp = trim((string) $request->get('competencia'))) {
            $query->where('competencia', 'like', "%{$comp}%");
        }
        if ($status = trim((string) $request->get('status'))) {
            $query->where('status', 'like', "%{$status}%");
        }

        $medicoes = $query->latest()->paginate(20)->appends($request->query());

        return view('medicoes.index', compact('medicoes'));
    }

    /**
     * Endpoint JSON para DataTables na listagem de medições.
     */
    public function data(Request $request)
    {
        $query = Medicao::with('contrato');

        // Filtros leves (contrato, competencia, status)
        if ($contratoTxt = trim((string) $request->get('contrato'))) {
            $query->whereHas('contrato', function ($q) use ($contratoTxt) {
                $q->where('numero', 'like', "%$contratoTxt%");
            });
        }
        if ($comp = trim((string) $request->get('competencia'))) {
            $query->where('competencia', 'like', "%$comp%");
        }
        if ($status = trim((string) $request->get('status'))) {
            $query->where('status', 'like', "%$status%");
        }

        $medicoes = $query->latest()->get();

        $data = $medicoes->map(function (Medicao $m) {
            return [
                'id' => $m->id,
                'contrato' => $m->contrato->numero ?? '—',
                'competencia' => $m->competencia,
                'tipo' => strtoupper($m->tipo),
                'valor_liquido' => $m->valor_liquido ?? 0,
                'status' => $m->status,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Redireciona detalhes da medição para a tela de comparação de documentos.
     */
    public function show($id)
    {
        return redirect()->route('medicoes.documentos.comparacao', ['medicao' => $id]);
    }

    public function create(Contrato $contrato)
    {
        // tipo sugerido pelo contrato (campo tipo_medicao)
        $tipo = $contrato->tipo_medicao ?? 'software';

        return view('medicoes.create', compact('contrato', 'tipo'));
    }

    public function store(Request $request, Contrato $contrato)
    {
        $data = $request->validate([
            'competencia' => 'required',
            'tipo' => 'required|in:software,telco,fixo_mensal',
        ]);

        $medicao = Medicao::create([
            'contrato_id' => $contrato->id,
            'competencia' => $data['competencia'],
            'tipo' => $data['tipo'],
            'status' => 'rascunho',
        ]);

        // Notificação: medição criada
        notify_event('notificacoes.medicoes.medicao_criada', [
            'titulo' => 'Medição criada',
            'mensagem' => "Medição {$medicao->id} criada para o contrato {$contrato->numero}",
        ], $medicao);

        // delega para fluxo especializado
        return match ($medicao->tipo) {
            'software' => $this->storeSoftware($request, $medicao),
            'telco' => $this->storeTelco($request, $medicao),
            'fixo_mensal' => $this->storeFixo($request, $medicao),
        };
    }

    protected function storeSoftware(Request $request, Medicao $medicao)
    {
        // recebe itens via formulário ou upload planilha/JSON
        $itens = $request->input('itens', []);

        $valorBruto = 0;
        $inconsistencias = [];

        foreach ($itens as $i) {
            $item = MedicaoItemSoftware::create([
                'medicao_id' => $medicao->id,
                'descricao' => $i['descricao'],
                'pf' => (int) $i['pf'],
                'ust' => (int) $i['ust'],
                'horas' => (float) $i['horas'],
                'qtd_pessoas' => (int) $i['qtd_pessoas'],
                'valor_unitario_pf' => $i['valor_unitario_pf'] ?? null,
                'valor_unitario_ust' => $i['valor_unitario_ust'] ?? null,
                'valor_total' => $i['valor_total'] ?? 0,
            ]);
            $valorBruto += $item->valor_total ?? 0;
        }

        // chama serviço de validação antifraude PF/UST
        $validator = app(\App\Services\MedicaoSoftwareValidator::class);
        $inconsistencias = $validator->validar($medicao);

        $medicao->update([
            'valor_bruto' => $valorBruto,
            'valor_liquido' => $valorBruto,
            'inconsistencias_json' => $inconsistencias,
        ]);

        return redirect()->route('medicoes.show', $medicao);
    }

    protected function storeTelco(Request $request, Medicao $medicao)
    {
        $itens = $request->input('itens', []);

        $valorBruto = 0;
        $inconsistencias = [];

        foreach ($itens as $i) {
            $item = MedicaoItemTelco::create([
                'medicao_id' => $medicao->id,
                'escola_id' => $i['escola_id'] ?? null,
                'localidade' => $i['localidade'] ?? null,
                'link_id' => $i['link_id'] ?? null,
                'uptime_percent' => $i['uptime_percent'],
                'downtime_minutos' => $i['downtime_minutos'],
                'qtd_quedas' => $i['qtd_quedas'],
                'valor_mensal_contratado' => $i['valor_mensal_contratado'],
                'valor_desconto' => $i['valor_desconto'] ?? 0,
                'valor_final' => $i['valor_final'] ?? 0,
                'eventos_json' => $i['eventos_json'] ?? null,
            ]);

            $valorBruto += $item->valor_mensal_contratado ?? 0;
        }

        // serviço que calcula SLA médio e penalidades
        $validator = app(MedicaoTelcoValidator::class);
        $resultado = $validator->validar($medicao);

        $medicao->update([
            'sla_alcancado' => $resultado['sla_alcancado'],
            'sla_contratado' => $resultado['sla_contratado'],
            'valor_bruto' => $valorBruto,
            'valor_desconto' => $resultado['desconto_total'],
            'valor_liquido' => $valorBruto - $resultado['desconto_total'],
            'inconsistencias_json' => $resultado['inconsistencias'] ?? [],
        ]);

        return redirect()->route('medicoes.show', $medicao);
    }

    protected function storeFixo(Request $request, Medicao $medicao)
    {
        $i = $request->input('item', []);

        $item = MedicaoItemFixoMensal::create([
            'medicao_id' => $medicao->id,
            'descricao' => $i['descricao'] ?? 'Medição mensal',
            'servico_prestado' => $i['servico_prestado'] ?? true,
            'relatorio_entregue' => $i['relatorio_entregue'] ?? true,
            'chamados_atendidos' => $i['chamados_atendidos'] ?? true,
            'chamados_pendentes' => $i['chamados_pendentes'] ?? 0,
            'valor_mensal_contratado' => $i['valor_mensal_contratado'],
            'valor_desconto' => $i['valor_desconto'] ?? 0,
            'valor_final' => $i['valor_final'] ?? $i['valor_mensal_contratado'],
            'observacoes_json' => $i['observacoes_json'] ?? null,
        ]);

        $validator = app(MedicaoFixoValidator::class);
        $resultado = $validator->validar($medicao);

        $medicao->update([
            'valor_bruto' => $item->valor_mensal_contratado,
            'valor_desconto' => $resultado['desconto_total'],
            'valor_liquido' => $item->valor_mensal_contratado - $resultado['desconto_total'],
            'inconsistencias_json' => $resultado['inconsistencias'] ?? [],
        ]);

        return redirect()->route('medicoes.show', $medicao);
    }
}
