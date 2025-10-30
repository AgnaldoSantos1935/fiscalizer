<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\ContratoItem;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratoController extends Controller
{
    /**
     * Exibe a listagem de contratos com filtros e DataTables.
     */
    public function index(Request $request)
    {
        $query = Contrato::with('empresa')
            ->when($request->numero, fn($q) => $q->where('numero', 'like', "%{$request->numero}%"))
            ->when($request->objeto, fn($q) => $q->where('objeto', 'like', "%{$request->objeto}%"))
            ->when($request->situacao, fn($q) => $q->where('situacao', $request->situacao))
            ->orderBy('data_inicio', 'desc');

        $contratos = $query->get();

        return view('contratos.index', compact('contratos'));
    }

    /**
     * Exibe o formulário de criação de contrato.
     */
    public function create()
    {
        $empresas = Empresa::orderBy('razao_social')->get();
        return view('contratos.create', compact('empresas'));
    }

    /**
     * Armazena um novo contrato e seus itens.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:50|unique:contratos',
            'objeto' => 'required|string',
            'contratada_id' => 'required|exists:empresas,id',
            'valor_global' => 'nullable|numeric',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'situacao' => 'nullable|string|max:50',
            'tipo' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request) {
            $contrato = Contrato::create($request->only([
                'numero', 'objeto', 'contratada_id', 'fiscal_tecnico_id', 'fiscal_administrativo_id',
                'gestor_id', 'valor_global', 'data_inicio', 'data_fim', 'situacao', 'tipo'
            ]));

            if ($request->has('itens')) {
                foreach ($request->itens as $item) {
                    $contrato->itens()->create([
                        'descricao_item' => $item['descricao_item'],
                        'unidade_medida' => $item['unidade_medida'] ?? null,
                        'quantidade' => $item['quantidade'] ?? 0,
                        'valor_unitario' => $item['valor_unitario'] ?? 0,
                        'tipo_item' => $item['tipo_item'] ?? 'servico',
                    ]);
                }
            }
        });

        return redirect()->route('contratos.index')->with('success', 'Contrato cadastrado com sucesso!');
    }

    /**
     * Exibe os detalhes de um contrato com seus itens.
     */
    public function show(Contrato $contrato)
    {
        $contrato->load(['empresa', 'itens']);
        return view('contratos.show', compact('contrato'));
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Contrato $contrato)
    {
        $contrato->load('itens');
        $empresas = Empresa::orderBy('razao_social')->get();
        return view('contratos.edit', compact('contrato', 'empresas'));
    }

    /**
     * Atualiza um contrato e seus itens.
     */
    public function update(Request $request, Contrato $contrato)
    {
        $request->validate([
            'numero' => 'required|string|max:50|unique:contratos,numero,' . $contrato->id,
            'objeto' => 'required|string',
            'contratada_id' => 'required|exists:empresas,id',
            'valor_global' => 'nullable|numeric',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        DB::transaction(function () use ($contrato, $request) {
            $contrato->update($request->only([
                'numero', 'objeto', 'contratada_id', 'fiscal_tecnico_id', 'fiscal_administrativo_id',
                'gestor_id', 'valor_global', 'data_inicio', 'data_fim', 'situacao', 'tipo'
            ]));

            // Remove itens antigos e recria (padrão simples)
            $contrato->itens()->delete();

            if ($request->has('itens')) {
                foreach ($request->itens as $item) {
                    $contrato->itens()->create([
                        'descricao_item' => $item['descricao_item'],
                        'unidade_medida' => $item['unidade_medida'] ?? null,
                        'quantidade' => $item['quantidade'] ?? 0,
                        'valor_unitario' => $item['valor_unitario'] ?? 0,
                        'tipo_item' => $item['tipo_item'] ?? 'servico',
                    ]);
                }
            }
        });

        return redirect()->route('contratos.show', $contrato)->with('success', 'Contrato atualizado com sucesso!');
    }

    /**
     * Remove um contrato e seus itens.
     */
    public function destroy(Contrato $contrato)
    {
        $contrato->delete();
        return redirect()->route('contratos.index')->with('success', 'Contrato excluído com sucesso!');
    }

    /**
     * Retorna detalhes JSON (para uso em AJAX ou modais).
     */
    public function detalhes($id)
    {
        $contrato = Contrato::with(['empresa', 'itens'])->findOrFail($id);
        return response()->json(['contrato' => $contrato]);
    }
}
