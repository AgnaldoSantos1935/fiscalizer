<?php

namespace App\Http\Controllers;

use App\Models\Situacao;
use App\Models\SituacaoContrato;
use Illuminate\Http\Request;

class SituacaoContratoController extends Controller
{
    /**
     * 🔹 Lista todas as situações
     */
    public function index()
    {
        $situacoes = SituacaoContrato::orderBy('id', 'asc')->get();

        return view('situacoes.index', compact('situacoes'));
    }

    public function listar()
    {
        $situacoes = SituacaoContrato::select('id', 'nome', 'descricao', 'slug', 'cor', 'motivo')
            ->orderBy('nome')
            ->get();

        return response()->json($situacoes);
    }

    /**
     * 🔹 Exibe o formulário de criação
     */
    public function create()
    {
        return view('situacoes.create');
    }

    /**
     * 🔹 Salva uma nova situação
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100|unique:situacoes',
            'descricao' => 'nullable|string|max:255',
            'cor' => 'nullable|string|max:20',
            'ativo' => 'nullable|boolean',
        ]);

        Situacao::create($validated);

        return redirect()
            ->route('situacoes.index')
            ->with('success', 'Situação cadastrada com sucesso!');
    }

    /**
     * 🔹 Exibe detalhes de uma situação
     */
    public function show($id)
    {
        $situacao = Situacao::findOrFail($id);

        return view('situacoes.show', compact('situacao'));
    }

    /**
     * 🔹 Exibe o formulário de edição
     */
    public function edit($id)
    {
        $situacao = Situacao::findOrFail($id);

        return view('situacoes.edit', compact('situacao'));
    }

    /**
     * 🔹 Atualiza uma situação existente
     */
    public function update(Request $request, $id)
    {
        $situacao = Situacao::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:100|unique:situacoes,nome,' . $situacao->id,
            'descricao' => 'nullable|string|max:255',
            'cor' => 'nullable|string|max:20',
            'ativo' => 'nullable|boolean',
        ]);

        $situacao->update($validated);

        return redirect()
            ->route('situacoes.index')
            ->with('success', 'Situação atualizada com sucesso!');
    }

    /**
     * 🔹 Remove uma situação
     */
    public function destroy($id)
    {
        $situacao = Situacao::findOrFail($id);
        $situacao->delete();

        return redirect()
            ->route('situacoes.index')
            ->with('success', 'Situação excluída com sucesso!');
    }
}
