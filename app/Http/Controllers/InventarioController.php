<?php
namespace App\Http\Controllers;

use App\Models\Unidade;
use App\Models\Equipamento;
use App\Models\ContratoItem;
use App\Models\Host;
use App\Models\Dre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventarioController extends Controller
{
    public function selecionarUnidade()
    {
        $dres = Dre::orderBy('nome_dre')->get(['id','nome_dre','municipio_sede']);

        return view('unidades.select', compact('dres'));
    }

    public function acessarPorDre(Dre $dre)
    {
        $unidade = \App\Models\Unidade::firstOrCreate(
            ['nome' => $dre->nome_dre, 'tipo' => 'Regional'],
            ['telefone' => null, 'inventario_token' => null]
        );

        return redirect()->route('unidades.inventario', $unidade->id);
    }

    public function index(Request $r, Unidade $unidade)
    {
        $tipo = trim(strtolower($unidade->tipo ?? ''));
        if ($tipo !== 'regional' && !str_contains($tipo, 'regional')) {
            abort(403, 'Acesso permitido somente para unidades regionais.');
        }
        $equipamentos = Equipamento::where('unidade_id', $unidade->id)->get();
        $hosts = Host::where('unidade_id', $unidade->id)->orderBy('nome_conexao')->get();
        $itens = ContratoItem::orderBy('descricao_item')->get(['id','descricao_item']);

        $dre = Dre::where('nome_dre', $unidade->nome)->first();
        $municipio = $r->get('municipio');
        $escolas = collect();
        $municipios = collect();
        if ($dre) {
            $escolas = \App\Models\Escola::where('dre', $dre->codigodre)
                ->when($municipio, function ($q) use ($municipio) { $q->where('municipio', $municipio); })
                ->orderBy('municipio')
                ->orderBy('escola')
                ->limit(500)
                ->get(['id','escola','municipio','telefone']);
            $municipios = \App\Models\Escola::where('dre', $dre->codigodre)
                ->select('municipio')
                ->distinct()
                ->orderBy('municipio')
                ->pluck('municipio');
        }

        $ocorrencias = \App\Models\EquipamentoOcorrencia::whereIn('equipamento_id', $equipamentos->pluck('id'))
            ->latest()
            ->limit(50)
            ->get();
        $reposicoes = \App\Models\ReposicaoSolicitacao::where('unidade_id', $unidade->id)
            ->latest()
            ->limit(50)
            ->get();

        return view('unidades.inventario', compact('unidade','equipamentos','hosts','itens','dre','escolas','municipios','municipio','ocorrencias','reposicoes'));
    }

    public function store(Request $r, Unidade $unidade)
    {
        $data = $r->validate([
            'origem' => 'required|in:centralizada,local',
            'tipo_equipamento' => 'required|string',
            'contrato_item_id' => 'nullable|exists:contrato_itens,id',
            'serial_number' => 'nullable|string',
            'hostname' => 'nullable|string',
            'sistema_operacional' => 'nullable|string',
            'cpu_resumida' => 'nullable|string',
            'ram_gb' => 'nullable|integer',
            'ip_atual' => 'nullable|string',
            'discos' => 'nullable|array',
            // compra local
            'descricao_local' => 'nullable|string',
            'documento_local' => 'nullable|string',
            'valor_local' => 'nullable|numeric',
            'data_aquisicao' => 'nullable|date',
            'especificacoes_local' => 'nullable|array',
        ]);

        $especificacoes = [];
        $origemStr = null;

        if ($data['origem'] === 'centralizada' && $data['contrato_item_id']) {
            $item = \App\Models\ContratoItem::find($data['contrato_item_id']);
            $desc = $item?->descricao_item ?? '';
            // parsing simples
            if (preg_match('/i[3579]|ryzen\s*\d+/i', $desc, $m)) { $especificacoes['cpu'] = $m[0]; }
            if (preg_match('/(\d+)\s*GB/i', $desc, $m)) { $especificacoes['ram_gb'] = (int) $m[1]; }
            if (preg_match('/(SSD|HDD)[^\d]*(\d+)\s*GB/i', $desc, $m)) { $especificacoes['armazenamento'] = $m[2] . ' GB ' . $m[1]; }
            $origemStr = 'contrato_item:' . $item->id;
        } elseif ($data['origem'] === 'local' && ($data['descricao_local'] ?? null)) {
            $idLocal = \DB::table('unidade_aquisicao_itens')->insertGetId([
                'unidade_id' => $unidade->id,
                'tipo' => $data['tipo_equipamento'],
                'descricao' => $data['descricao_local'],
                'especificacoes' => json_encode($data['especificacoes_local'] ?? []),
                'documento' => $data['documento_local'] ?? null,
                'valor' => $data['valor_local'] ?? null,
                'data_aquisicao' => $data['data_aquisicao'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $origemStr = 'compra_unidade:' . $idLocal;
            $especificacoes = array_merge($especificacoes, ($data['especificacoes_local'] ?? []));
        }

        Equipamento::create([
            'unidade_id' => $unidade->id,
            'tipo' => $data['tipo_equipamento'],
            'serial_number' => $data['serial_number'] ?? null,
            'hostname' => $data['hostname'] ?? null,
            'sistema_operacional' => $data['sistema_operacional'] ?? null,
            'cpu_resumida' => $data['cpu_resumida'] ?? ($especificacoes['cpu'] ?? null),
            'ram_gb' => $data['ram_gb'] ?? ($especificacoes['ram_gb'] ?? null),
            'ip_atual' => $data['ip_atual'] ?? null,
            'discos' => $data['discos'] ?? null,
            'especificacoes' => $especificacoes,
            'origem_inventario' => $origemStr,
        ]);

        return redirect()->route('unidades.inventario', $unidade->id)->with('success','Equipamento incluído.');
    }

    public function reportarQuebra(Request $r, Equipamento $equipamento)
    {
        $r->validate(['descricao' => 'nullable|string']);
        \App\Models\EquipamentoOcorrencia::create([
            'equipamento_id' => $equipamento->id,
            'tipo' => 'quebra',
            'descricao' => $r->input('descricao'),
            'status' => 'aberta',
            'reportado_by' => auth()->id(),
        ]);

        return back()->with('success','Quebra registrada.');
    }

    public function solicitarReposicao(Request $r, Unidade $unidade)
    {
        $data = $r->validate([
            'equipamento_id' => 'nullable|exists:equipamentos,id',
            'contrato_item_id' => 'required|exists:contrato_itens,id',
            'quantidade' => 'required|integer|min:1',
            'motivo' => 'nullable|string',
        ]);

        $rep = \App\Models\ReposicaoSolicitacao::create([
            'unidade_id' => $unidade->id,
            'equipamento_id' => $data['equipamento_id'] ?? null,
            'contrato_item_id' => $data['contrato_item_id'],
            'quantidade' => $data['quantidade'],
            'status' => 'pendente',
            'motivo' => $data['motivo'] ?? null,
        ]);

        \App\Models\EquipamentoReposicaoHistorico::create([
            'unidade_id' => $unidade->id,
            'equipamento_id' => $data['equipamento_id'] ?? null,
            'reposicao_id' => $rep->id,
            'evento' => 'solicitada',
            'usuario_id' => auth()->id(),
            'observacoes' => $data['motivo'] ?? null,
        ]);

        return back()->with('success','Reposição solicitada.');
    }

    public function citReceberOcorrencia(Request $r, \App\Models\EquipamentoOcorrencia $ocorrencia)
    {
        $ocorrencia->update([
            'status' => 'recebida',
            'recebida_por' => auth()->id(),
            'analise_status' => 'em_analise',
        ]);

        return back()->with('success','Ocorrência recebida pelo CIT.');
    }

    public function citAvaliarOcorrencia(Request $r, \App\Models\EquipamentoOcorrencia $ocorrencia)
    {
        $data = $r->validate([
            'decisao' => 'required|in:reposicao,sem_reposicao',
            'observacoes' => 'nullable|string',
        ]);
        $ocorrencia->update([
            'avaliada_por' => auth()->id(),
            'analise_status' => $data['decisao'] === 'reposicao' ? 'reposicao_sugerida' : 'sem_reposicao',
            'analise_observacoes' => $data['observacoes'] ?? null,
            'status' => 'em_analise',
        ]);

        return back()->with('success','Ocorrência avaliada pelo CIT.');
    }

    public function detecAprovarReposicao(Request $r, \App\Models\ReposicaoSolicitacao $reposicao)
    {
        \App\Models\LogSistema::create([
            'usuario_id' => auth()->id(),
            'acao' => 'equipamentos.reposicao_aprovada',
            'detalhes' => json_encode(['reposicao_id' => $reposicao->id]),
        ]);
        $reposicao->update([
            'status' => 'aprovada',
            'detec_usuario_id' => auth()->id(),
            'aprovada_em' => now(),
        ]);

        \App\Models\EquipamentoReposicaoHistorico::create([
            'unidade_id' => $reposicao->unidade_id,
            'equipamento_id' => $reposicao->equipamento_id,
            'reposicao_id' => $reposicao->id,
            'evento' => 'aprovada',
            'usuario_id' => auth()->id(),
        ]);

        return back()->with('success','Reposição aprovada pela DETEC.');
    }

    public function detecRegistrarEntrega(Request $r, \App\Models\ReposicaoSolicitacao $reposicao)
    {
        \App\Models\LogSistema::create([
            'usuario_id' => auth()->id(),
            'acao' => 'equipamentos.reposicao_entregue',
            'detalhes' => json_encode(['reposicao_id' => $reposicao->id]),
        ]);
        $reposicao->update([
            'status' => 'entregue',
            'entregue_em' => now(),
        ]);

        \App\Models\EquipamentoReposicaoHistorico::create([
            'unidade_id' => $reposicao->unidade_id,
            'equipamento_id' => $reposicao->equipamento_id,
            'reposicao_id' => $reposicao->id,
            'evento' => 'entregue',
            'usuario_id' => auth()->id(),
        ]);

        return back()->with('success','Entrega registrada.');
    }

    public function detecBaixarEquipamento(Request $r, Equipamento $equipamento, \App\Models\ReposicaoSolicitacao $reposicao)
    {
        \App\Models\LogSistema::create([
            'usuario_id' => auth()->id(),
            'acao' => 'equipamentos.reposicao_baixa',
            'detalhes' => json_encode(['equipamento_id' => $equipamento->id, 'reposicao_id' => $reposicao->id]),
        ]);
        $reposicao->update([
            'status' => 'baixado',
            'baixado_em' => now(),
        ]);

        \App\Models\EquipamentoReposicaoHistorico::create([
            'unidade_id' => $reposicao->unidade_id,
            'equipamento_id' => $equipamento->id,
            'reposicao_id' => $reposicao->id,
            'evento' => 'baixa',
            'usuario_id' => auth()->id(),
        ]);

        return back()->with('success','Baixa do equipamento registrada.');
    }

    public function storeConexao(Request $r, Unidade $unidade)
    {
        $data = $r->validate([
            'nome_conexao' => 'required|string',
            'host_alvo' => 'required|string',
            'tipo_monitoramento' => 'required|string',
            'provedor' => 'nullable|string',
            'tecnologia' => 'nullable|string',
            'contrato_item_id' => 'nullable|exists:contrato_itens,id',
        ]);

        Host::create([
            'unidade_id' => $unidade->id,
            'nome_conexao' => $data['nome_conexao'],
            'host_alvo' => $data['host_alvo'],
            'tipo_monitoramento' => $data['tipo_monitoramento'],
            'provedor' => $data['provedor'] ?? null,
            'tecnologia' => $data['tecnologia'] ?? null,
            'itemcontratado' => $data['contrato_item_id'] ?? null,
        ]);

        return redirect()->route('unidades.inventario', $unidade->id)->with('success','Conexão cadastrada.');
    }

    public function gerarEspecificacoes(Request $r, Unidade $unidade)
    {
        $tipo = trim(strtolower($unidade->tipo ?? ''));
        if ($tipo !== 'regional' && !str_contains($tipo, 'regional')) {
            abort(403);
        }

        $dre = Dre::where('nome_dre', $unidade->nome)->first();
        $municipio = $r->get('municipio');
        $params = [
            'baseline_computers' => (int) ($r->get('baseline_computers', 10)),
            'computers_per_ap' => (int) ($r->get('computers_per_ap', 20)),
            'safety_factor' => (float) ($r->get('safety_factor', 1.2)),
            'meters_per_drop' => (int) ($r->get('meters_per_drop', 30)),
        ];

        $escolas = collect();
        if ($dre) {
            $escolas = \App\Models\Escola::where('dre', $dre->codigodre)
                ->when($municipio, fn($q) => $q->where('municipio', $municipio))
                ->orderBy('escola')
                ->get(['id','escola','municipio']);
        }

        $items = [];
        $totais = [
            'computadores' => 0,
            'aps' => 0,
            'switch_ports' => 0,
            'cabo_drops' => 0,
            'cabo_metros' => 0,
        ];

        foreach ($escolas as $esc) {
            $computadores = $params['baseline_computers'];
            $aps = (int) ceil($computadores / max(1, $params['computers_per_ap']));
            $switch_ports = max(24, (int) ceil($computadores * $params['safety_factor']));
            $drops = $switch_ports; // 1 ponto por porta
            $metros = $drops * $params['meters_per_drop'];

            $items[] = [
                'escola' => $esc->escola,
                'municipio' => $esc->municipio,
                'computadores' => $computadores,
                'aps' => $aps,
                'switch_ports' => $switch_ports,
                'cabo_drops' => $drops,
                'cabo_metros' => $metros,
            ];

            $totais['computadores'] += $computadores;
            $totais['aps'] += $aps;
            $totais['switch_ports'] += $switch_ports;
            $totais['cabo_drops'] += $drops;
            $totais['cabo_metros'] += $metros;
        }

        $spec = [
            'unidade' => $unidade->nome,
            'dre' => $dre?->nome_dre,
            'params' => $params,
            'items' => $items,
            'totais' => $totais,
            'recomendacoes' => [
                'switch_modelo' => 'Gerenciável, 24 portas, 2xSFP, VLAN, QoS',
                'ap_modelo' => 'Dual-band, 802.11ac/ax, PoE, controlador',
                'cabo' => 'UTP Cat6, patch panel e pontos RJ45 padrão',
            ],
            'normas' => [
                'ABNT NBR 14565 — Cabeamento de telecomunicações para edifícios comerciais',
                'ISO/IEC 11801 — Generic cabling for customer premises',
                'ANSI/TIA-568.2-D — Balanced twisted-pair cabling components',
                'ANSI/TIA-606-C — Administração de infraestrutura de telecomunicações',
                'IEEE 802.3af/at/bt — Power over Ethernet',
                'IEEE 802.11ac/ax — Redes WLAN de alta capacidade',
                'ABNT NBR 5410 — Instalações elétricas de baixa tensão',
                'ABNT NBR ISO/IEC 27001 — Sistema de gestão de segurança da informação',
                'ANATEL — Equipamentos com homologação vigente',
            ],
        ];

        $normasEngine = app(\App\Services\NormasRecomendacaoService::class);
        $normOut = $normasEngine->recomendar($spec);
        $spec['motor'] = $normOut;

        $rag = app(\App\Services\NormasRagService::class);
        $filtrosBase = [
            'idioma' => $r->get('idioma', 'pt-BR'),
        ];
        if ($r->filled('fonte')) { $filtrosBase['fonte'] = $r->get('fonte'); }
        if ($r->filled('tags')) { $filtrosBase['tags'] = (array) $r->get('tags'); }
        $fund1 = $rag->justificar('Utilizar cabo Cat6 para garantir desempenho gigabit.', $filtrosBase);
        $fund2 = $rag->justificar('Justifique porque a escola precisa de AP Wi‑Fi 6 (802.11ax).', $filtrosBase);
        $spec['fundamentacao'] = [
            'cabo_cat6_gigabit' => $fund1,
            'wifi6_densidade' => $fund2,
        ];

        if ($r->filled('rag_q')) {
            $rq = trim($r->get('rag_q'));
            $rf = $r->get('rag_fonte');
            $rt = (array) $r->get('rag_tags');
            $filtros = [ 'idioma' => $r->get('idioma', 'pt-BR') ];
            if (!empty($rf)) { $filtros['fonte'] = $rf; }
            if (!empty($rt)) { $filtros['tags'] = $rt; }
            $spec['rag_busca'] = [
                'consulta' => $rq,
                'resultados' => $rag->buscarFundamentacao($rq, 8, $filtros),
            ];
        }

        $byMunicipio = collect($items)->groupBy('municipio')->map(function ($list) {
            return [
                'computadores' => collect($list)->sum('computadores'),
                'aps' => collect($list)->sum('aps'),
                'switch_ports' => collect($list)->sum('switch_ports'),
                'cabo_metros' => collect($list)->sum('cabo_metros'),
            ];
        })->sortByDesc('computadores');
        $totalComp = max(1, (int) $totais['computadores']);
        $dash = $byMunicipio->map(function ($v) use ($totalComp) {
            $pct = round(($v['computadores'] / $totalComp) * 100);
            return array_merge($v, ['pct' => $pct]);
        });
        $spec['dashboard'] = [
            'municipios' => $dash,
            'total' => $totais,
        ];

        if ($r->get('format') === 'json') {
            return response()->json($spec);
        }

        return view('unidades.especificacoes', compact('unidade','dre','municipio','params','spec'));
    }

    public function uploadNorma(Request $r, Unidade $unidade)
    {
        $data = $r->validate([
            'arquivo' => 'required|file|mimes:pdf',
            'fonte' => 'nullable|string',
            'idioma' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $path = Storage::putFile('normas', $data['arquivo']);
        $full = storage_path('app/' . $path);

        $proc = app(\App\Services\NormaTecnicaProcessorService::class);
        $count = $proc->indexarPdf($full, [
            'fonte' => $data['fonte'] ?? null,
            'idioma' => $data['idioma'] ?? 'pt-BR',
            'tags' => $data['tags'] ?? [],
        ]);

        return redirect()->route('unidades.especificacoes', $unidade->id)
            ->with('success', 'Norma lida com sucesso (' . $count . ' trechos).');
    }
}
