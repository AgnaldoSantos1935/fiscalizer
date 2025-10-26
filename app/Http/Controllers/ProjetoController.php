<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use App\Models\Contrato;
use Illuminate\Http\Request;

class ProjetoController extends Controller
{
    /**
     * Lista todos os projetos
     */
    public function index()
    {
        $projetos = Projeto::with('contrato')->orderBy('id', 'desc')->paginate(10);
        return view('projetos.index', compact('projetos'));
    }

    /**
     * Exibe formulário de criação
     */
    public function create()
    {
        $contratos = Contrato::all();
        return view('projetos.create', compact('contratos'));
    }

    /**
     * Salva novo projeto
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'contrato_id' => 'required|exists:contratos,id',
        ]);

        Projeto::create([
            'contrato_id' => $request->contrato_id,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'status' => $request->status ?? 'planejado',
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('projetos.index')->with('success', 'Projeto criado com sucesso!');
    }

    /**
     * Exibe detalhes
     */
    public function show(Projeto $projeto)
    {
        $projeto->load('contrato', 'itensMedicao');
        return view('projetos.show', compact('projeto'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Projeto $projeto)
    {
        $contratos = Contrato::all();
        return view('projetos.edit', compact('projeto', 'contratos'));
    }

    /**
     * Atualiza registro
     */
    public function update(Request $request, Projeto $projeto)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'contrato_id' => 'required|exists:contratos,id',
        ]);

        $projeto->update([
            'contrato_id' => $request->contrato_id,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'status' => $request->status,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('projetos.index')->with('success', 'Projeto atualizado com sucesso!');
    }

    /**
     * Remove registro
     */
    public function destroy(Projeto $projeto)
    {
        $projeto->delete();
        return redirect()->route('projetos.index')->with('success', 'Projeto excluído com sucesso!');
    }
}
