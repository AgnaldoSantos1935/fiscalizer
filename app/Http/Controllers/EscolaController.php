<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\Dre;
use Illuminate\Http\Request;

class EscolaController extends Controller
{
    /**
     * Retorna dados para DataTables (AJAX)
     */
public function getData()
{
    $escolas = Escola::select([
        'id', 'inep', 'Escola', 'Municipio', 'UF', 'dre'
    ])
    ->with('dre:id,nome_dre')
    ->orderBy('Escola', 'asc')
    ->get()
    ->map(function ($escola) {
        return [
            'id' => $escola->id,
            'inep' => $escola->inep,
            'Escola' => $escola->Escola,
            'Municipio' => $escola->Municipio,
            'UF' => $escola->UF,
            'dre_nome' => $escola->dre->nome_dre ?? '-'
        ];
    });

    return response()->json(['data' => $escolas]);
}



    /**
     * Lista as escolas
     */
    public function index()
    {
        $escolas = Escola::with('dre')->orderBy('Escola')->get();
        return view('escolas.index', compact('escolas'));
    }

    /**
     * Exibe formulário de criação
     */
    public function create()
    {
        $dres = Dre::orderBy('nome_dre')->get(['id', 'nome_dre']);
        return view('escolas.create', compact('dres'));
    }

    /**
     * Armazena nova escola (via AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inep' => 'required|string|max:20|unique:escolas',
            'nome' => 'required|string|max:255',
            'municipio' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'dre' => 'nullable|exists:dres,id',
        ]);

        Escola::create([
            'inep' => $validated['inep'],
            'Escola' => $validated['nome'],
            'Municipio' => $validated['municipio'] ?? null,
            'UF' => strtoupper($validated['uf'] ?? 'PA'),
            'Telefone' => $validated['telefone'] ?? null,
            'Endereco' => $validated['endereco'] ?? null,
            'dre_id' => $validated['dre'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Exibe detalhes da escola
     */
public function show($id)
{
    $escola = Escola::with('dre')->findOrFail($id);

    if (request()->ajax()) {
        return response()->json(['escola' => $escola]); // ✅ sempre retorna JSON
    }

    return view('escolas.show', compact('escola'));
}


    /**
     * Exibe formulário de edição
     */
    public function edit(Escola $escola)
    {
        $dres = Dre::orderBy('nome_dre')->get(['id', 'nome_dre']);
        return view('escolas.edit', compact('escola', 'dres'));
    }

    /**
     * Atualiza dados
     */
    public function update(Request $request, Escola $escola)
    {
        $validated = $request->validate([
            'inep' => 'required|string|max:20|unique:escolas,codigo,' . $escola->id,
            'nome' => 'required|string|max:255',
            'municipio' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'dre' => 'nullable|exists:dres,id',
        ]);

        $escola->update([
            'inep' => $validated['inep'],
            'Escola' => $validated['nome'],
            'Municipio' => $validated['municipio'] ?? null,
            'UF' => strtoupper($validated['uf'] ?? 'PA'),
            'Telefone' => $validated['telefone'] ?? null,
            'Endereco' => $validated['endereco'] ?? null,
            'dre_id' => $validated['dre'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove uma escola
     */
    public function destroy(Escola $escola)
    {
        try {
            $escola->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir: ' . $e->getMessage()
            ], 500);
        }
    }
}
