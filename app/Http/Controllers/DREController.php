<?php

namespace App\Http\Controllers;

use App\Models\Dre;
use Illuminate\Http\Request;

class DreController extends Controller
{
    /**
     * Lista todas as DREs.
     */
    public function index()
    {
        $dres = Dre::all();
        return view('dres.index', compact('dres'));
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create()
    {
        return view('dres.create');
    }

    /**
     * Armazena uma nova DRE no banco.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigodre'       => 'required|string|max:10|unique:dres,codigodre',
            'nome_dre'        => 'required|string|max:150',
            'municipio_sede'  => 'required|string|max:100',
            'email'           => 'nullable|email|max:150',
            'telefone'        => 'nullable|string|max:50',
            'endereco'        => 'nullable|string|max:255',
        ]);

        Dre::create($request->all());

        return redirect()
            ->route('dres.index')
            ->with('success', 'DRE criada com sucesso.');
    }

    /**
     * Retorna uma DRE específica em JSON (para modais e fetch()).
     */
    public function show($id)
    {
        $dre = Dre::find($id);

        if (!$dre) {
            return response()->json(['error' => 'DRE não encontrada.'], 404);
        }

        return response()->json(['dre' => $dre]);
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit($id)
    {
        $dre = Dre::findOrFail($id);
        return view('dres.edit', compact('dre'));
    }

    /**
     * Atualiza uma DRE existente.
     */
    public function update(Request $request, $id)
    {
        $dre = Dre::findOrFail($id);

        $request->validate([
            'codigodre'       => 'required|string|max:10|unique:dres,codigodre,' . $dre->id,
            'nome_dre'        => 'required|string|max:150',
            'municipio_sede'  => 'required|string|max:100',
            'email'           => 'nullable|email|max:150',
            'telefone'        => 'nullable|string|max:50',
            'endereco'        => 'nullable|string|max:255',
        ]);

        $dre->update($request->all());

        return redirect()
            ->route('dres.index')
            ->with('success', 'DRE atualizada com sucesso.');
    }

    /**
     * Remove uma DRE.
     */
public function destroy($id)
{
    $dre = \App\Models\Dre::find($id);

    if (!$dre) {
        return response()->json(['error' => 'DRE não encontrada.'], 404);
    }

    $dre->delete();
    return response()->json(['success' => true]);
}

}
