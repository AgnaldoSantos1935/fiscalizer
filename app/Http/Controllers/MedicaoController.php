<?php

namespace App\Http\Controllers;

use App\Models\Medicao;
use App\Models\Contrato;
use Illuminate\Http\Request;

class MedicaoController extends Controller
{
    public function index()
    {
        $medicoes = Medicao::with('contrato')->get();
        return view('medicoes.index', compact('medicoes'));
    }

    public function create()
    {
        $contratos = Contrato::all();
        return view('medicoes.create', compact('contratos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'mes_referencia' => 'required|string|max:7',
            'valor_unitario_pf' => 'required|numeric|min:0',
        ]);

        Medicao::create($validated);
        return redirect()->route('medicoes.index')->with('success', 'Medição criada com sucesso!');
    }

    public function show(Medicao $medicao)
    {
         $medicao = \App\Models\Medicao::with('funcoes', 'contrato')->findOrFail($id);
            return view('medicoes.show', compact('medicao'));
    }
}
