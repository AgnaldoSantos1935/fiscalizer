<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Empresa;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
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
}
