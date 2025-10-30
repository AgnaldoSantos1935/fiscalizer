<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::all();
        return view('empresas.index', compact('empresas'));
    }

  public function create()
{
    return view('empresas.create');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'razao_social' => 'required|string|max:255',
        'cnpj' => 'required|string|max:20|unique:empresas,cnpj',
    ]);

    Empresa::create($validated + $request->except(['_token']));
    return redirect()->route('empresas.index')->with('success', 'Empresa cadastrada com sucesso!');
}

public function show(Empresa $empresa)
{
    return view('empresas.show', compact('empresa'));
}


    public function edit(Empresa $empresa)
{
    return view('empresas.edit', compact('empresa'));
}

public function update(Request $request, Empresa $empresa)
{
    $validated = $request->validate([
        'razao_social' => 'required|string|max:255',
        'cnpj' => 'required|string|max:20|unique:empresas,cnpj,' . $empresa->id,
    ]);

    $empresa->update($validated + $request->except(['_token', '_method']));
    return redirect()->route('empresas.index')->with('success', 'Dados atualizados com sucesso!');
}


    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('empresas.index')->with('success', 'Empresa excluída com sucesso!');
    }
}
