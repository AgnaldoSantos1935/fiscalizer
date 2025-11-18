<?php

namespace App\Http\Controllers;

use App\Models\FuncaoSistema;
use App\Models\Medicao;
use Illuminate\Http\Request;

class FuncaoSistemaController extends Controller
{
    public function create(Medicao $medicao)
    {
        return view('funcoes.create', compact('medicao'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medicao_id' => 'required|exists:medicoes,id',
            'nome_funcao' => 'required|string|max:150',
            'tipo' => 'required|string',
            'complexidade' => 'required|string',
        ]);

        FuncaoSistema::create($validated);

        return redirect()->route('medicoes.show', $validated['medicao_id'])
            ->with('success', 'Função cadastrada com sucesso!');
    }
}
