<?php

namespace App\Http\Controllers;

use App\Models\AtividadeTecnica;
use Illuminate\Http\Request;

class AtividadeController extends Controller
{
public function store(Request $request)
{
    $data = $request->validate([
        'projeto_id' => 'required|exists:projetos,id',
        'etapa'      => 'required|string|max:255',
        'analista'   => 'nullable|string|max:150',
        'data'       => 'nullable|date',
        'horas'      => 'nullable|numeric',
        'descricao'  => 'nullable|string|max:255',
    ]);

    AtividadeTecnica::create($data);

    return redirect()
        ->route('projetos.show', $data['projeto_id'])
        ->with('success', 'Atividade registrada com sucesso!');
}


    public function update(Request $request, AtividadeTecnica $atividade)
    {
        $data = $request->validate([
            'etapa'      => 'required|string|max:255',
            'analista'   => 'nullable|string|max:150',
            'data'       => 'nullable|date',
            'horas'      => 'nullable|numeric',
            'descricao'  => 'nullable|string|max:255',
        ]);

        $atividade->update($data);
        return response()->json(['success' => true, 'data' => $atividade]);
    }

    public function destroy(AtividadeTecnica $atividade)
    {
        $atividade->delete();
        return response()->json(['success' => true]);
    }
    public function show($id)
{
    $model = AtividadeTecnica::findOrFail($id); // substitua conforme o controller
    return response()->json($model);
}
}
