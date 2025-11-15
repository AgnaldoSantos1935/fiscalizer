<?php

namespace App\Http\Controllers;

use App\Models\Medicao;
use App\Models\MedicaoItemTelco;
use App\Models\MedicaoItemSoftware;
use App\Models\MedicaoItemFixoMensal;
use App\Services\ValidacaoMedicaoService;
use \App\Services\MedicaoSoftwareValidator;
use \App\Services\MedicaoTelcoValidador;
use App\Models\Contrato;
use Illuminate\Http\Request;

class MedicaoController extends Controller
{
    public function index()
    {
        $medicoes = Medicao::with('contrato')->latest()->paginate(20);
        return view('medicoes.index', compact('medicoes'));
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
            'tipo'        => 'required|in:software,telco,fixo_mensal',
        ]);

        $medicao = Medicao::create([
            'contrato_id' => $contrato->id,
            'competencia' => $data['competencia'],
            'tipo'        => $data['tipo'],
            'status'      => 'rascunho',
        ]);

        // delega para fluxo especializado
        return match ($medicao->tipo) {
            'software'    => $this->storeSoftware($request, $medicao),
            'telco'       => $this->storeTelco($request, $medicao),
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
                'descricao'  => $i['descricao'],
                'pf'         => (int) $i['pf'],
                'ust'        => (int) $i['ust'],
                'horas'      => (float) $i['horas'],
                'qtd_pessoas'=> (int) $i['qtd_pessoas'],
                'valor_unitario_pf'  => $i['valor_unitario_pf'] ?? null,
                'valor_unitario_ust' => $i['valor_unitario_ust'] ?? null,
                'valor_total'        => $i['valor_total'] ?? 0,
            ]);
            $valorBruto += $item->valor_total ?? 0;
        }

        // chama serviço de validação antifraude PF/UST
        $validator = app(\App\Services\MedicaoSoftwareValidator::class);
        $inconsistencias = $validator->validar($medicao);

        $medicao->update([
            'valor_bruto'         => $valorBruto,
            'valor_liquido'       => $valorBruto,
            'inconsistencias_json'=> $inconsistencias,
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
                'medicao_id'              => $medicao->id,
                'escola_id'               => $i['escola_id'] ?? null,
                'localidade'              => $i['localidade'] ?? null,
                'link_id'                 => $i['link_id'] ?? null,
                'uptime_percent'          => $i['uptime_percent'],
                'downtime_minutos'        => $i['downtime_minutos'],
                'qtd_quedas'              => $i['qtd_quedas'],
                'valor_mensal_contratado' => $i['valor_mensal_contratado'],
                'valor_desconto'          => $i['valor_desconto'] ?? 0,
                'valor_final'             => $i['valor_final'] ?? 0,
                'eventos_json'            => $i['eventos_json'] ?? null,
            ]);

            $valorBruto += $item->valor_mensal_contratado ?? 0;
        }

        // serviço que calcula SLA médio e penalidades
        $validator = app(MedicaoTelcoValidator::class);
        $resultado = $validator->validar($medicao);

        $medicao->update([
            'sla_alcancado'       => $resultado['sla_alcancado'],
            'sla_contratado'      => $resultado['sla_contratado'],
            'valor_bruto'         => $valorBruto,
            'valor_desconto'      => $resultado['desconto_total'],
            'valor_liquido'       => $valorBruto - $resultado['desconto_total'],
            'inconsistencias_json'=> $resultado['inconsistencias'] ?? [],
        ]);

        return redirect()->route('medicoes.show', $medicao);
    }

    protected function storeFixo(Request $request, Medicao $medicao)
    {
        $i = $request->input('item', []);

        $item = MedicaoItemFixoMensal::create([
            'medicao_id'               => $medicao->id,
            'descricao'                => $i['descricao'] ?? 'Medição mensal',
            'servico_prestado'         => $i['servico_prestado'] ?? true,
            'relatorio_entregue'       => $i['relatorio_entregue'] ?? true,
            'chamados_atendidos'       => $i['chamados_atendidos'] ?? true,
            'chamados_pendentes'       => $i['chamados_pendentes'] ?? 0,
            'valor_mensal_contratado'  => $i['valor_mensal_contratado'],
            'valor_desconto'           => $i['valor_desconto'] ?? 0,
            'valor_final'              => $i['valor_final'] ?? $i['valor_mensal_contratado'],
            'observacoes_json'         => $i['observacoes_json'] ?? null,
        ]);

        $validator = app(MedicaoFixoValidator::class);
        $resultado = $validator->validar($medicao);

        $medicao->update([
            'valor_bruto'         => $item->valor_mensal_contratado,
            'valor_desconto'      => $resultado['desconto_total'],
            'valor_liquido'       => $item->valor_mensal_contratado - $resultado['desconto_total'],
            'inconsistencias_json'=> $resultado['inconsistencias'] ?? [],
        ]);

        return redirect()->route('medicoes.show', $medicao);
    }
}
