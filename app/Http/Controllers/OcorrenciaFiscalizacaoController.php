<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\OcorrenciaFiscalizacao;
use Illuminate\Http\Request;

class OcorrenciaFiscalizacaoController extends Controller
{
    public function index()
    {
        $ocorrencias = OcorrenciaFiscalizacao::with('contrato')->get();

        return view('ocorrencias.index', compact('ocorrencias'));
    }

    public function create()
    {
        $contratos = Contrato::all();

        return view('ocorrencias.create', compact('contratos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'tipo' => 'required|string',
            'descricao' => 'required|string',
            'data_ocorrencia' => 'nullable|date',
        ]);

        OcorrenciaFiscalizacao::create($validated);

        return redirect()->route('ocorrencias.index')->with('success', 'Ocorrência registrada com sucesso!');
    }
}
