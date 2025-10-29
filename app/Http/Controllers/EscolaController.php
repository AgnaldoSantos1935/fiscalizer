<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use Illuminate\Http\Request;

class EscolaController extends Controller
{
    /**
     * Exibe a lista de escolas.
     */
    public function getData()
{
    $escolas = Escola::select(['codigo', 'Escola', 'Municipio', 'UF', 'inep', 'Telefone'])
        ->orderBy('Escola', 'asc')
        ->get();

    return response()->json(['data' => $escolas]);
}

    public function index()
    {
        // Busca todas as escolas ordenadas por nome
        $escolas = Escola::all();

        return view('escolas.index', compact('escolas'));
    }

    /**
     * Exibe o formulário de criação (caso precise página separada).
     */
    public function create()
    {
        return view('escolas.create');
    }

    /**
     * Armazena uma nova escola.
     */
    public function store(Request $request)
    {
        // ✅ Validação básica — você pode expandir conforme o schema
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:escolas,codigo',
            'nome' => 'required|string|max:255',
            'municipio' => 'nullable|string|max:150',
            'uf' => 'nullable|string|max:2',
            'codigo_inep' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
        ]);

        // ✅ Criação direta via mass assignment
        Escola::create($validated);

        return redirect()
            ->route('escolas.index')
            ->with('success', 'Escola cadastrada com sucesso!');
    }

    /**
     * Exibe os detalhes de uma escola.
     */
 public function show($id)
{
    $escola = Escola::findOrFail($id);

    if (request()->ajax()) {
        return response()->json($escola);
    }

    return view('escolas.show', compact('escola'));
}


    /**
     * Exibe o formulário de edição (não usado pois usa modal).
     */
    public function edit(Escola $escola)
    {
        return view('escolas.edit', compact('escola'));
    }

    /**
     * Atualiza uma escola existente.
     */
    public function update(Request $request, $id)
    {
        // ✅ Validação com regra de exclusão do próprio registro
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:escolas,codigo,' . $id,
            'nome' => 'required|string|max:255',
            'municipio' => 'nullable|string|max:150',
            'uf' => 'nullable|string|max:2',
            'codigo_inep' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
        ]);

        $escola->update($validated);

        return redirect()
            ->route('escolas.index')
            ->with('success', 'Dados da escola atualizados com sucesso!');
    }

    /**
     * Remove uma escola.
     */
    public function destroy(Escola $escola)
    {
        $escola->delete();

        return redirect()
            ->route('escolas.index')
            ->with('success', 'Escola excluída com sucesso!');
    }
}
