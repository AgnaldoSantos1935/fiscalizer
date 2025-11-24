<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function index()
    {
        $actions = Action::orderBy('modulo')->orderBy('codigo')->paginate(15);

        return view('actions.index', compact('actions'));
    }

    public function create()
    {
        return view('actions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:100|unique:actions,codigo',
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:500',
            'modulo' => 'nullable|string|max:100',
        ]);

        $action = Action::create($data);

        return redirect()->route('actions.show', $action)->with('success', 'Action criada com sucesso.');
    }

    public function show(Action $action)
    {
        return view('actions.show', compact('action'));
    }

    public function edit(Action $action)
    {
        return view('actions.edit', compact('action'));
    }

    public function update(Request $request, Action $action)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:100|unique:actions,codigo,' . $action->id,
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:500',
            'modulo' => 'nullable|string|max:100',
        ]);

        $action->update($data);

        return redirect()->route('actions.show', $action)->with('success', 'Action atualizada com sucesso.');
    }

    public function destroy(Action $action)
    {
        $action->delete();

        return redirect()->route('actions.index')->with('success', 'Action excluída com sucesso.');
    }
}
