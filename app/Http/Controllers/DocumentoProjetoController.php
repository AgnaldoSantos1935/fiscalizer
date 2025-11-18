<?php

namespace App\Http\Controllers;

use App\Models\DocumentoProjeto;
use App\Models\FiscalizacaoProjeto;
use Illuminate\Http\Request;

class DocumentoProjetoController extends Controller
{
    public function store(Request $request, FiscalizacaoProjeto $fiscalizacao)
    {
        $request->validate([
            'tipo' => 'nullable|string|max:100',
            'titulo' => 'nullable|string|max:255',
            'arquivo' => 'required|file|max:20480', // 20MB
        ]);
        $path = $request->file('arquivo')->store('fiscalizacoes', 'public');
        DocumentoProjeto::create([
            'fiscalizacao_id' => $fiscalizacao->id,
            'tipo' => $request->input('tipo'),
            'titulo' => $request->input('titulo'),
            'arquivo' => $path,
        ]);

        return back()->with('success', 'Documento anexado.');
    }
}
