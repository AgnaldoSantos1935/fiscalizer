<?php

namespace App\Http\Controllers;

use App\Models\EquipeProjeto;
use Illuminate\Http\Request;

class EquipeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'pessoa_id' => 'required|exists:pessoas,id',
            // Renomeado no frontend: aceitar "perfil" e mapear para coluna "papel"
            'perfil' => 'nullable|string|max:100',
            'horas_previstas' => 'nullable|numeric',
            'horas_realizadas' => 'nullable|numeric',
        ]);
        // Usar diretamente o atributo "perfil" (coluna renomeada)

        $membro = EquipeProjeto::create($data);

        return response()->json(['success' => true, 'data' => $membro]);
    }

    public function update(Request $request, EquipeProjeto $equipe)
    {
        $data = $request->validate([
            'perfil' => 'nullable|string|max:100',
            'horas_previstas' => 'nullable|numeric',
            'horas_realizadas' => 'nullable|numeric',
        ]);
        // Usar diretamente o atributo "perfil" (coluna renomeada)

        $equipe->update($data);

        return response()->json(['success' => true, 'data' => $equipe]);
    }

    public function destroy(EquipeProjeto $equipe)
    {
        $equipe->delete();

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $model = EquipeProjeto::findOrFail($id); // substitua conforme o controller

        return response()->json($model);
    }
}
