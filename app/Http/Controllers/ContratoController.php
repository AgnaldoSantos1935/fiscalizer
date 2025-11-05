<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\Pessoa;
use App\Models\Situacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratoController extends Controller
{
    /**
     * 🔹 Lista todos os contratos
     */
    // ContratoController
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


    public function index()
    {
        $contratos = Contrato::with(['contratada'])->orderBy('id', 'desc')->get();
        return view('contratos.index', compact('contratos'));
    }

    /**
     * 🔹 Exibe um contrato específico
     */
    public function show($id)
    {
        $contrato = Contrato::with([
            'contratada',
            'fiscalTecnico',
            'fiscalAdministrativo',
            'gestor',
            'empenhos',
            'itens'
        ])->findOrFail($id);

        return view('contratos.show', compact('contrato'));
    }

    /**
     * 🔹 Exibe o formulário de criação
     */
    public function create()
    {
        $empresas   = Empresa::orderBy('razao_social')->get();
        $pessoas    = Pessoa::orderBy('nome')->get();

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
        $contrato   = Contrato::findOrFail($id);
        $empresas   = Empresa::orderBy('razao_social')->get();
        $pessoas    = Pessoa::orderBy('nome')->get();
        $situacoes  = Situacao::orderBy('nome')->get();

        return view('contratos.edit', compact('contrato', 'empresas', 'pessoas', 'situacoes'));
    }

    /**
     * 🔹 Atualiza um contrato
     */
    public function update(Request $request, $id)
    {
        $contrato = Contrato::findOrFail($id);

        $validated = $request->validate([
            'numero' => 'required|string|max:30|unique:contratos,numero,' . $contrato->id,
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
            'situacao' => 'required|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $contrato->update($validated);

        return redirect()
            ->route('contratos.index')
            ->with('success', 'Contrato atualizado com sucesso!');
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

    /**
     * 🔹 Retorna os itens via AJAX (modal)
     */
    public function getItens($id)
    {
        $contrato = Contrato::with('itens')->findOrFail($id);

        return response()->json([
            'contrato' => $contrato->numero,
            'itens' => $contrato->itens->map(function ($item) {
                return [
                    'descricao' => $item->descricao_item,
                    'unidade' => $item->unidade_medida,
                    'quantidade' => $item->quantidade,
                    'valor_unitario' => number_format($item->valor_unitario, 2, ',', '.'),
                    'valor_total' => number_format($item->valor_total, 2, ',', '.'),
                    'status' => $item->status,
                ];
            }),
        ]);
    }

    /**
     * 🔹 Retorna JSON com itens + contrato (para modais de visualização)
     */
    public function itens($id)
    {
        $contrato = Contrato::with('itens')->findOrFail($id);

        return response()->json([
            'contrato' => $contrato,
            'itens' => $contrato->itens
        ]);
    }
}
