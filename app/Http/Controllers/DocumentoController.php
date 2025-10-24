<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Contrato;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with('contrato')->get();
        return view('documentos.index', compact('documentos'));
    }

    public function create()
    {
        $contratos = Contrato::all();
        return view('documentos.create', compact('contratos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'tipo' => 'required|string',
            'titulo' => 'nullable|string|max:200',
            'caminho_arquivo' => 'nullable|file',
        ]);

        Documento::create($validated);
        return redirect()->route('documentos.index')->with('success', 'Documento cadastrado!');
    }
}
