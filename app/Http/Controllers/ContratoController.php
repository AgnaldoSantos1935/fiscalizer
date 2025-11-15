<?php


namespace App\Http\Controllers;


use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Contrato;
use App\Models\SituacaoContrato;
use App\Models\Empresa;
use App\Models\Pessoa;
use App\Models\Situacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use yajra\laravel\datatables\oracle;
use function PHPUnit\Framework\assertDoesNotMatchRegularExpression;
use App\Services\ContratoRiscoService;

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
        try {
            $contrato = Contrato::with('contratada')->findOrFail($id);

            return response()->json([
                'id' => $contrato->id,
                'numero' => $contrato->numero,
                'objeto' => $contrato->objeto,
                'valor_global' => $contrato->valor_global,
                'situacao' => $contrato->situacao,
                'data_inicio' => $contrato->data_inicio,
                'data_fim' => $contrato->data_fim,
                'empresa' => [
                    'id' => $contrato->contratada->id ?? null,
                    'razao_social' => $contrato->contratada->razao_social ?? null,
                    'cnpj' => $contrato->contratada->cnpj ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Contrato não encontrado ou erro interno.',
                'message' => $e->getMessage(),
            ], 500);
        }
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
            'empenhos.pagamentos:id,empenho_id,valor_pagamento,data_pagamento,documento,observacao'
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
        ]);

        $validated['created_by'] = Auth::id();

        Contrato::create($validated);

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


}
