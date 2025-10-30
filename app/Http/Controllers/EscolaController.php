<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use Illuminate\Http\Request;

class EscolaController extends Controller
{
    public function index()
    {
        $escolas = Escola::all();
        return view('escolas.index', compact('escolas'));
    }

    public function create()
    {
        return view('escolas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:escolas',
            'nome' => 'required|string|max:255',
        ]);

        Escola::create($data + $request->except(['_token']));
        return redirect()->route('escolas.index')->with('success', 'Escola cadastrada com sucesso!');
    }

    public function show(Escola $escola)
    {
        return view('escolas.show', compact('escola'));
    }

    public function edit(Escola $escola)
    {
        return view('escolas.edit', compact('escola'));
    }

    public function update(Request $request, Escola $escola)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:escolas,codigo,' . $escola->id,
            'nome' => 'required|string|max:255',
        ]);

        $escola->update($data + $request->except(['_token', '_method']));
        return redirect()->route('escolas.index')->with('success', 'Escola atualizada com sucesso!');
    }

    public function destroy(Escola $escola)
    {
        $escola->delete();
        return redirect()->route('escolas.index')->with('success', 'Escola excluída com sucesso!');
    }
}
