<?php

namespace App\Http\Controllers;

use App\Models\FuncaoSistema;
use App\Models\RequisitoSistema;
use Illuminate\Http\Request;

class RequisitoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'descricao' => 'required|string|max:255',
            'tipo' => 'required|string|max:10',
            'complexidade' => 'required|string|max:20',
            'pontos_funcao' => 'nullable|numeric',
            'responsavel' => 'nullable|string|max:150',
        ]);

        $requisito = FuncaoSistema::create($data);

        return response()->json(['success' => true, 'data' => $requisito]);
    }

    public function update(Request $request, FuncaoSistema $requisito)
    {
        $data = $request->validate([
            'descricao' => 'required|string|max:255',
            'tipo' => 'required|string|max:10',
            'complexidade' => 'required|string|max:20',
            'pontos_funcao' => 'nullable|numeric',
            'responsavel' => 'nullable|string|max:150',
        ]);

        $requisito->update($data);

        return response()->json(['success' => true, 'data' => $requisito]);
    }

    public function destroy(FuncaoSistema $requisito)
    {
        $requisito->delete();

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $model = RequisitoSistema::findOrFail($id); // substitua conforme o controller

        return response()->json($model);
    }
}
