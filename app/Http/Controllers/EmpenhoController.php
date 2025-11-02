<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empenho;
use App\Models\Contrato;

class EmpenhoController extends Controller
{
    public function index()
    {
        // Lógica para listar os empenhos
        return view('empenhos.index');
    }

    /**
     * Retorna JSON com os empenhos para consumo via AJAX/DataTables
     */
    public function getData()
    {
        $empenhos = Empenho::with('contrato')->get()->map(function($e){
            return [
                'id' => $e->id,
                'numero' => $e->numero,
                'data_empenho' => $e->data_empenho ? $e->data_empenho->toDateString() : null,
                'valor' => $e->valor,
                'descricao' => $e->descricao,
                'contrato_id' => $e->contrato_id,
            ];
        });

        return response()->json($empenhos);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'contrato_id' => ['required', 'integer', 'exists:contratos,id'],
            'numero' => ['required', 'string', 'max:50', 'unique:empenhos,numero'],
            'data_empenho' => ['nullable', 'date'],
            'valor' => ['required', 'numeric', 'min:0'],
            'descricao' => ['nullable', 'string'],
            'projeto_atividade' => ['nullable', 'string', 'max:20'],
            'fonte_recurso' => ['nullable', 'string', 'max:50'],
        ]);

        $empenho = Empenho::create($data);

        return redirect()->route('contratos.show', $data['contrato_id'])
                         ->with('success', 'Empenho criado com sucesso.');
    }
    public function show($id)
    {
        // Lógica para mostrar um empenho específico
    }
    public function edit($id)
    {
        // Lógica para editar um empenho específico
    }
    public function update(Request $request, $id)
    {
        // Lógica para atualizar um empenho específico
    }
    public function destroy($id)
    {
        // Lógica para deletar um empenho específico
    }
    public function create(Request $request)
    {
        // aceita ?contrato_id= na query string para pré-associação
        $contrato = null;
        if ($request->has('contrato_id')) {
            $contrato = Contrato::find($request->get('contrato_id'));
            if (! $contrato) {
                return redirect()->back()->withErrors(['contrato_id' => 'Contrato não encontrado.']);
            }
        }

        return view('empenhos.create', compact('contrato'));
    }

}
