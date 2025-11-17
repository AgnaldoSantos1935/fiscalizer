<?php


namespace App\Http\Controllers;


use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Contrato;
use App\Models\SituacaoContrato;
use App\Models\Documento;
use App\Models\Empresa;
use App\Models\Pessoa;
use App\Models\Situacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use yajra\laravel\datatables\oracle;
use function PHPUnit\Framework\assertDoesNotMatchRegularExpression;
use App\Services\ContratoRiscoService;
use App\Services\IAContratoService;
use Illuminate\Support\Facades\Storage;
use App\Services\LeitorDocumentoService;

class ContratoController extends Controller
{

    public function getJsonContratos()
    {
        // 🔹 Carrega contratos com todas as relações importantes
        $contratos = Contrato::with([
            'contratada:id,razao_social,cnpj',
            'fiscalTecnico:id,nome_completo',
            'fiscalAdministrativo:id,nome_completo',
            'gestor:id,nome_completo',
            'situacaoContrato:id,nome,cor,slug'
        ])
            ->orderBy('id', 'desc')
            ->get();

        // 🔹 Estrutura de resposta formatada para o DataTables
        $dados = $contratos->map(function ($c) {
            return [
                'id' => $c->id,
                'numero' => $c->numero,
                'objeto' => $c->objeto,
                'valor_global' => (float) $c->valor_global,
                'data_inicio' => $c->data_inicio ? $c->data_inicio->format('Y-m-d') : null,
                'data_fim' => $c->data_fim ? $c->data_fim->format('Y-m-d') : null,

                // 🔸 Empresa contratada
                'contratada' => [
                    'id' => $c->contratada->id ?? null,
                    'razao_social' => $c->contratada->razao_social ?? null,
                    'cnpj' => $c->contratada->cnpj ?? null,
                ],

                // 🔸 Situação
                'situacao_contrato' => $c->situacaoContrato
                    ? [
                        'id' => $c->situacaoContrato->id,
                        'nome' => $c->situacaoContrato->nome,
                        'descricao' => $c->situacaoContrato->descricao,
                        'cor' => $c->situacaoContrato->cor,
                        'slug' => $c->situacaoContrato->slug,
                    ]
                    : null,

                // 🔸 Fiscais e gestor
                'fiscal_tecnico' => $c->fiscalTecnico->nome ?? null,
                'fiscal_administrativo' => $c->fiscalAdministrativo->nome ?? null,
                'gestor' => $c->gestor->nome ?? null,
            ];
        });

        // 🔹 Retorna JSON no formato aceito pelo DataTables
        return response()->json(['data' => $dados]);
    }

public function getContratoJson($id)
{
    // Se id for zero, string "0", null ou vazio → retorna todos
    if ((int) $id === 0) {
        return response()->json(
            Contrato::orderBy('numero')
                ->get(['id', 'numero', 'objeto'])
        );
    }

    // Se não for zero → retorna o contrato + itens
    $contrato = Contrato::with('itens', 'empresa')->findOrFail($id);

    return response()->json([
        'id' => $contrato->id,
        'numero' => $contrato->numero,
        'objeto' => $contrato->objeto,
        'valor_global' => $contrato->valor_global,
        'situacao' => $contrato->situacao,
        'data_inicio' => $contrato->data_inicio,
        'data_fim' => $contrato->data_fim,
        'empresa' => $contrato->empresa?->only(['id','razao_social','cnpj']),
        'itens' => $contrato->itens?->map(fn($i) => [
            'id' => $i->id,
            'descricao' => $i->descricao
        ]) ?? []
    ]);
}




    public function index(Request $request)
    {

        return view('contratos.index');
    }


    public function show($id)
    {
        return view('contratos.show', compact('id'));
    }

    public function detalhesContrato($id)
    {
        $contrato = Contrato::with([
            'contratada:id,razao_social,cnpj',
            'situacaoContrato:id,nome,cor,slug',
            'itens:id,contrato_id,descricao_item,unidade_medida,quantidade,valor_unitario,valor_total,tipo_item',
            'empenhos.pagamentos:id,empenho_id,valor_pagamento,data_pagamento,documento,observacao',
            'documentos:id,contrato_id,documento_tipo_id,tipo,titulo,descricao,caminho_arquivo,data_upload,nova_data_fim',
            'documentos.documentoTipo:id,nome,slug,permite_nova_data_fim'
        ])->findOrFail($id);

        return response()->json([
            'id' => $contrato->id,
            'numero' => $contrato->numero,
            'objeto' => $contrato->objeto,
            'valor_global' => $contrato->valor_global,
            'data_inicio' => $contrato->data_inicio,
            'data_fim' => $contrato->data_fim,
            'contratada' => $contrato->contratada,
            'data_final' => $contrato->data_final,
            'vigencia_meses' => $contrato->vigencia_meses,
            'modalidade' => $contrato->modalidade,
            'num_processo' => $contrato->num_processo,
            'situacao_contrato' => $contrato->situacaoContrato,
            'itens' => $contrato->itens,
            'empenhos' => $contrato->empenhos,
            'documentos' => $contrato->documentos->sortByDesc('id')->values(),
            'totais' => [
                'valor_empenhado' => $contrato->valor_empenhado,
                'valor_pago' => $contrato->valor_pago,
                'saldo' => $contrato->saldo_contrato,
            ]

        ]);

    }

    /**
     * 🔹 Exibe o formulário de criação
     */
    public function create()
    {
        $empresas = Empresa::orderBy('razao_social')->get();
        $pessoas = Pessoa::orderBy('nome_completo')->get();

        return view('contratos.create', compact('empresas', 'pessoas'));
    }

    /**
     * 🔹 Armazena um novo contrato
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:30|unique:contratos',
            'objeto' => 'required|string',
            'contratada_id' => 'required|exists:empresas,id',
            'fiscal_tecnico_id' => 'nullable|exists:pessoas,id',
            'fiscal_administrativo_id' => 'nullable|exists:pessoas,id',
            'gestor_id' => 'nullable|exists:pessoas,id',
            'valor_global' => 'required|numeric|min:0',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'situacao' => 'nullable|string|in:vigente,encerrado,rescindido,suspenso',
            'tipo' => 'nullable|string|in:TI,Serviço,Obra,Material',
            'situacao_id' => 'nullable|exists:situacoes,id',
            // vínculo opcional do PDF enviado na extração
            'documento_pdf_id' => 'nullable|exists:documentos,id',
        ]);

        $validated['created_by'] = Auth::id();

        $contrato = Contrato::create($validated);

        // Vincula documento PDF (se veio do fluxo de extração)
        if (!empty($validated['documento_pdf_id'])) {
            $doc = Documento::find($validated['documento_pdf_id']);
            if ($doc) {
                $doc->contrato_id = $contrato->id;
                $doc->updated_by = Auth::id();
                $doc->save();
            }
        }

        return redirect()
            ->route('contratos.index')
            ->with('success', 'Contrato cadastrado com sucesso!');
    }

    /**
     * 🔹 Editar contrato existente
     */
    public function edit($id)
    {
        $contrato = Contrato::findOrFail($id);
        $empresas = Empresa::orderBy('razao_social')->get();
        $pessoas = Pessoa::orderBy('nome')->get();
        $users = User::orderBy('name')->get(); // para escolher fiscais/gestor


        return view('contratos.edit', compact('contrato', 'empresas', 'users', 'situacoes'));
    }

    /**
     * Redireciona para o visualizador do PDF principal do contrato.
     */
    public function pdf(Contrato $contrato)
    {
        $doc = Documento::where('contrato_id', $contrato->id)
            ->where('tipo', 'contrato_pdf')
            ->orderByDesc('id')
            ->first();

        if (!$doc) {
            return redirect()->back()->with('error', 'Nenhum PDF de contrato encontrado.');
        }

        return redirect()->route('documentos.visualizar', [
            'documento' => $doc->id,
            'return_to' => route('contratos.show', $contrato->id),
        ]);
    }

    /**
     * Exibe a view de cadastro de documentos vinculados ao contrato.
     */
    public function createDocumento(Contrato $contrato)
    {
        $tipos = \App\Models\DocumentoTipo::where('ativo', true)
            ->orderBy('nome')
            ->get(['id','nome','slug','permite_nova_data_fim']);

        return view('contratos.documentos.create', [
            'contrato' => $contrato,
            'tipos' => $tipos,
        ]);
    }

    /**
     * 🔹 Atualiza um contrato
     */
    public function update(Request $request, Contrato $contrato)
    {
        $data = $request->validate([
            'numero' => 'required',
            'processo_origem' => 'nullable',
            'modalidade' => 'nullable',
            'objeto' => 'required',
            'objeto_resumido' => 'nullable',
            'valor_global' => 'nullable|numeric',
            'valor_mensal' => 'nullable|numeric',
            'quantidade_meses' => 'nullable|integer',
            'data_assinatura' => 'nullable|date',
            'data_inicio_vigencia' => 'nullable|date',
            'data_fim_vigencia' => 'nullable|date',
            'empresa_razao_social' => 'nullable',
            'empresa_cnpj' => 'nullable',
            'empresa_endereco' => 'nullable',
            'empresa_representante' => 'nullable',
            'empresa_contato' => 'nullable',
            'empresa_email' => 'nullable|email',
            'obrigacoes_contratada' => 'nullable',
            'obrigacoes_contratante' => 'nullable',
            'itens_fornecimento' => 'nullable',
            'clausulas' => 'nullable',
            'riscos_detectados' => 'nullable',
            'anexos_detectados' => 'nullable',
        ]);

        // campos JSON vêm como textarea (string) -> transforma em array se quiser
        foreach (['obrigacoes_contratada', 'obrigacoes_contratante', 'itens_fornecimento', 'clausulas', 'riscos_detectados', 'anexos_detectados'] as $campo) {
            if (isset($data[$campo]) && is_string($data[$campo])) {
                $data[$campo] = json_decode($data[$campo], true);
            }
        }
    }

    /**
     * 🔹 Exclui (soft delete)
     */
    public function destroy($id)
    {
        $contrato = Contrato::findOrFail($id);
        $contrato->delete();

        return redirect()
            ->route('contratos.index')
            ->with('success', 'Contrato removido com sucesso!');
    }

    private function syncFiscaisFromRequest(Request $request, Contrato $contrato)
    {
        $map = [];

        if ($request->filled('fiscal_tecnico_id')) {
            $map[$request->input('fiscal_tecnico_id')] = ['tipo' => 'fiscal_tecnico'];
        }
        if ($request->filled('fiscal_administrativo_id')) {
            $map[$request->input('fiscal_administrativo_id')] = ['tipo' => 'fiscal_administrativo'];
        }
        if ($request->filled('gestor_id')) {
            $map[$request->input('gestor_id')] = ['tipo' => 'gestor'];
        }

        $contrato->fiscais()->sync($map);
    }
public function salvar(Request $request, ContratoRiscoService $riscoService)
{
    $data = $request->all();

    $contrato = new Contrato();
    $contrato->fill($data);

    // calcula risco antes de salvar
    $inconsistenciasIa = json_decode($data['inconsistencias_json'] ?? '[]', true);
    $resultadoRisco = $riscoService->calcular($contrato, $inconsistenciasIa);

    $contrato->risco_score = $resultadoRisco['score'];
    $contrato->risco_nivel = $resultadoRisco['nivel'];
    $contrato->risco_detalhes_json = json_encode($resultadoRisco['detalhes']);

    $contrato->save();

    // ...
}

    /**
     * Extração automática de dados a partir de um PDF enviado.
     * Retorna JSON com campos compatíveis com o formulário de criação.
     */
    public function extrair(Request $request, IAContratoService $ia)
    {
        $request->validate([
            // Usar mimes:pdf para maior compatibilidade
            'pdf' => 'required|file|mimes:pdf|max:51200',
        ]);

        $file = $request->file('pdf');
        // Salva uma cópia definitiva do PDF
        $uuid = (string) Str::uuid();
        $ext = strtolower($file->getClientOriginalExtension() ?? 'pdf');
        $safeOriginal = preg_replace('/[^a-zA-Z0-9_\-\.]+/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $finalName = $safeOriginal ? ("{$safeOriginal}_{$uuid}.{$ext}") : ("contrato_{$uuid}.{$ext}");
        $savedPath = $file->storeAs('contratos/originais', $finalName, 'public');

        // Usa caminho físico para leitura/extracao
        $fullPath = Storage::disk('public')->path($savedPath);

        $resultado = null;
        // Tenta IA somente se houver chave configurada
        $apiKey = config('services.openai.key');
        if (!empty($apiKey)) {
            try {
                $resultado = $ia->processarContrato($path, $file->getClientOriginalName());
            } catch (\Throwable $e) {
                // Continua para fallback local
                $resultado = null;
                $iaErro = $e->getMessage();
            }
        } else {
            $iaErro = 'OPENAI_API_KEY ausente';
        }

        // Alguns serviços retornam em ['contrato' => ...]
        $dados = $resultado['contrato'] ?? $resultado ?? [];

        // Mapeia possíveis nomes vindos da IA para os campos do formulário
        $numero = $dados['numero'] ?? null;
        $objeto = $dados['objeto'] ?? ($dados['descricao_objeto'] ?? null);
        $valorGlobal = $dados['valor_global'] ?? ($dados['valor_total'] ?? null);
        $dataInicio = $dados['data_inicio'] ?? ($dados['data_inicio_vigencia'] ?? ($dados['data_assinatura'] ?? null));
        $dataFim = $dados['data_fim'] ?? ($dados['data_fim_vigencia'] ?? null);

        // Tenta localizar empresa pela informação extraída (CNPJ ou razão social)
        $contratadaId = null;
        $empresaInfo = $dados['empresa'] ?? $dados['contratada'] ?? [];
        if (!empty($empresaInfo)) {
            $cnpj = $empresaInfo['cnpj'] ?? null;
            $razao = $empresaInfo['razao_social'] ?? ($empresaInfo['nome'] ?? null);
            if ($cnpj) {
                $empresa = Empresa::where('cnpj', $cnpj)->first();
                $contratadaId = $empresa?->id;
            }
            if (!$contratadaId && $razao) {
                $empresa = Empresa::where('razao_social', 'like', $razao)->first();
                $contratadaId = $empresa?->id;
            }
        }

        // Normaliza datas para YYYY-MM-DD quando possível
        $normalizaData = function ($data) {
            if (!$data) return null;
            try {
                return (new \Carbon\Carbon($data))->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        };

        // Fallback local: tenta extrair pelo texto do PDF
        if (!$numero || !$valorGlobal || !$objeto || (!$dataInicio && !$dataFim)) {
            try {
                $texto = (new LeitorDocumentoService())->extrairPdf($fullPath);

                // Número do contrato
                if (!$numero) {
                    if (preg_match('/(?:Contrato\s*(?:N[ºo]|No|N°)?\s*|Número\s*:?)\s*([\w\-\.\/]+)/i', $texto, $m)) {
                        $numero = $m[1] ?? $numero;
                    }
                }

                // Valor global
                if (!$valorGlobal) {
                    if (preg_match('/(?:Valor\s+(?:Global|Total)|Valor\s+do\s+Contrato)[^\d]*(R\$\s*[\d\.\,]+)/i', $texto, $m)) {
                        $valorStr = $m[1];
                        $valorStr = preg_replace('/[^\d\,\.]/', '', $valorStr);
                        // Converte padrão pt-BR para float
                        $valorGlobal = floatval(str_replace(['.', ','], ['', '.'], preg_replace('/\.(?=.*\.)/', '', $valorStr)));
                    }
                }

                // Datas (pega duas primeiras datas no texto)
                if (!$dataInicio || !$dataFim) {
                    preg_match_all('/\b(\d{2}\/\d{2}\/\d{4})\b/', $texto, $datas);
                    if (!empty($datas[1])) {
                        $dataInicio = $dataInicio ?: $datas[1][0] ?? null;
                        $dataFim    = $dataFim ?: ($datas[1][1] ?? null);
                    }
                }

                // Objeto (pega trecho após "Objeto")
                if (!$objeto) {
                    if (preg_match('/Objeto\s*:\s*(.+)/i', $texto, $m)) {
                        $objeto = trim($m[1]);
                    }
                }

                // Empresa por CNPJ
                if (!$contratadaId) {
                    if (preg_match('/\b\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}\b/', $texto, $m)) {
                        $cnpjClean = preg_replace('/\D/', '', $m[0]);
                        $empresa = Empresa::where('cnpj', $cnpjClean)->first();
                        $contratadaId = $empresa?->id ?? $contratadaId;
                    }
                }
            } catch (\Throwable $e) {
                // Ignora fallback se falhar; apenas segue com o que já temos
            }
        }

        // Registra o documento na tabela 'documentos' (sem contrato ainda)
        $documento = Documento::create([
            'contrato_id'   => null,
            'tipo'          => 'contrato_pdf',
            'titulo'        => $file->getClientOriginalName(),
            'descricao'     => 'Cópia do contrato em PDF enviada na criação',
            'caminho_arquivo'=> $savedPath,
            'versao'        => null,
            'data_upload'   => now(),
            'created_by'    => Auth::id(),
        ]);

        $payload = [
            'ok' => true,
            'fields' => [
                'numero' => $numero,
                'valor_global' => $valorGlobal,
                'objeto' => $objeto,
                'data_inicio' => $normalizaData($dataInicio),
                'data_fim' => $normalizaData($dataFim),
                'contratada_id' => $contratadaId,
            ],
            'empresa_extraida' => $empresaInfo,
            'ia_status' => !empty($resultado) ? 'ok' : 'fallback',
            'ia_error' => $iaErro ?? null,
            // retorno do documento armazenado
            'documento_id' => $documento->id ?? null,
            'documento_path' => $savedPath,
            'documento_nome' => $file->getClientOriginalName(),
        ];

        return response()->json($payload);
    }

    /**
     * Anexa um PDF ao contrato já cadastrado.
     */
    public function uploadPdf(Request $request, Contrato $contrato)
    {
        $data = $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:51200',
            'documento_tipo_id' => 'required|exists:documento_tipos,id',
            'titulo' => 'nullable|string|max:200',
            'descricao' => 'nullable|string|max:500',
            'nova_data_fim' => 'nullable|date',
        ]);

        $file = $request->file('pdf');
        $tipoEntidade = \App\Models\DocumentoTipo::findOrFail($data['documento_tipo_id']);
        $uuid = (string) Str::uuid();
        $ext = strtolower($file->getClientOriginalExtension() ?? 'pdf');
        $safeOriginal = preg_replace('/[^a-zA-Z0-9_\-\.]+/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $finalName = $safeOriginal ? ("{$safeOriginal}_{$uuid}.{$ext}") : ("contrato_{$uuid}.{$ext}");
        $savedPath = $file->storeAs('contratos/originais', $finalName, 'public');

        $novaDataFim = $tipoEntidade->permite_nova_data_fim ? ($data['nova_data_fim'] ?? null) : null;

        Documento::create([
            'contrato_id'    => $contrato->id,
            'tipo'           => $tipoEntidade->slug,
            'documento_tipo_id' => $tipoEntidade->id,
            'titulo'         => $request->input('titulo') ?: $file->getClientOriginalName(),
            'descricao'      => $request->input('descricao') ?: 'Documento anexado na tela de detalhes do contrato',
            'caminho_arquivo'=> $savedPath,
            'versao'         => null,
            'data_upload'    => now(),
            'nova_data_fim'  => $novaDataFim,
            'created_by'     => \Illuminate\Support\Facades\Auth::id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Documento do contrato anexado com sucesso.');
    }


}
