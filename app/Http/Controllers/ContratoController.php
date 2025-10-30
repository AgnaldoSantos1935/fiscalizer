<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Empresa;
use Illuminate\Http\Request;

class ContratoController extends Controller
{

    public function getItens($id)
{
    $contrato = \App\Models\Contrato::with('itens')->findOrFail($id);

    // Retorna JSON para o modal
    return response()->json([
        'contrato' => $contrato,
        'itens' => $contrato->itens
    ]);
}

    public function index()
    {
        $contratos = Contrato::with('contratada')->get();
        return view('contratos.index', compact('contratos'));
    }
  public function show()
    {
    $contratos = Contrato::with('contratada')->get();
        return view('contratos.index', compact('contratos'));
    }
    public function create()
    {
        $empresas = Empresa::all();
        return view('contratos.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:30',
            'objeto' => 'required|string',
            'contratada_id' => 'required|exists:empresas,id',
            'valor_global' => 'required|numeric|min:0',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
        ]);

        Contrato::create($validated);
        return redirect()->route('contratos.index')->with('success', 'Contrato cadastrado com sucesso!');
    }

public function itens($id)
{
    $contrato = \App\Models\Contrato::with('itens')->findOrFail($id);

    // Retorna JSON para o AJAX preencher o modal
    return response()->json([
        'contrato' => $contrato->numero,
        'itens' => $contrato->itens->map(function($item) {
            return [
                'descricao' => $item->descricao_item,
                'unidade' => $item->unidade_medida,
                'quantidade' => $item->quantidade,
                'valor_unitario' => number_format($item->valor_unitario, 2, ',', '.'),
                'valor_total' => number_format($item->valor_total, 2, ',', '.'),
                'status' => $item->status
            ];
        })
    ]);
}
}
