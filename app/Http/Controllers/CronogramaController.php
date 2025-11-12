<?php

namespace App\Http\Controllers;

use App\Models\CronogramaProjeto;
use Illuminate\Http\Request;

class CronogramaController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'projeto_id'  => 'required|exists:projetos,id',
            'etapa'       => 'required|string|max:255',
            'responsavel' => 'nullable|string|max:150',
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date',
            'status'      => 'nullable|string|max:50',
        ]);

        $cronograma = CronogramaProjeto::create($data);
        return response()->json(['success' => true, 'data' => $cronograma]);
    }

    public function update(Request $request, CronogramaProjeto $cronograma)
    {
        $data = $request->validate([
            'etapa'       => 'required|string|max:255',
            'responsavel' => 'nullable|string|max:150',
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date',
            'status'      => 'nullable|string|max:50',
        ]);

        $cronograma->update($data);
        return response()->json(['success' => true, 'data' => $cronograma]);
    }

    public function destroy(CronogramaProjeto $cronograma)
    {
        $cronograma->delete();
        return response()->json(['success' => true]);
    }
    public function show($id)
{
    $model = CronogramaProjeto::findOrFail($id); // substitua conforme o controller
    return response()->json($model);
}
}
