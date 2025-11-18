<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\RequisitoSistema;
use Illuminate\Http\Request;

class DemandaController extends Controller
{
    public function index()
    {
        $demandas = Demanda::withCount('requisitos')
            ->latest('data_abertura')
            ->paginate(20);

        return view('demandas.index', compact('demandas'));
    }

    public function create()
    {
        return view('demandas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'projeto_id' => 'nullable|integer',
            'sistema_id' => 'nullable|integer',
            'modulo_id' => 'nullable|integer',
            'tipo_manutencao' => 'required|string',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_abertura' => 'nullable|date',
            'prioridade' => 'required|string',
        ]);

        $data['status'] = 'aberta';

        $demanda = Demanda::create($data);

        return redirect()
            ->route('demandas.show', $demanda)
            ->with('success', 'Demanda criada com sucesso.');
    }

    public function show(Demanda $demanda)
    {
        $demanda->load('requisitos');

        return view('demandas.show', compact('demanda'));
    }

    public function edit(Demanda $demanda)
    {
        return view('demandas.edit', compact('demanda'));
    }

    public function update(Request $request, Demanda $demanda)
    {
        $data = $request->validate([
            'tipo_manutencao' => 'required|string',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_abertura' => 'nullable|date',
            'data_fechamento' => 'nullable|date',
            'prioridade' => 'required|string',
            'status' => 'required|string',
        ]);

        $demanda->update($data);

        return redirect()
            ->route('demandas.show', $demanda)
            ->with('success', 'Demanda atualizada.');
    }

    public function destroy(Demanda $demanda)
    {
        $demanda->delete();

        return redirect()
            ->route('demandas.index')
            ->with('success', 'Demanda excluída.');
    }

    /*
     * CRUD rápido de requisitos amarrados à demanda
     */

    public function addRequisito(Request $request, Demanda $demanda)
    {
        $data = $request->validate([
            'codigo_interno' => 'nullable|string|max:50',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'etapa' => 'nullable|string|max:50',
            'tipo' => 'nullable|string|max:50',
            'complexidade' => 'nullable|string|max:50',
        ]);

        $data['demanda_id'] = $demanda->id;

        RequisitoSistema::create($data);

        return back()->with('success', 'Requisito incluído.');
    }

    public function deleteRequisito(Demanda $demanda, RequisitoSistema $requisito)
    {
        if ($requisito->demanda_id != $demanda->id) {
            abort(403);
        }

        $requisito->delete();

        return back()->with('success', 'Requisito excluído.');
    }
}
