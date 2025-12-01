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
    public function index(Request $request)
    {
        $query = SituacaoContrato::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->input('nome') . '%');
        }
        if ($request->filled('slug')) {
            $query->where('slug', 'like', '%' . $request->input('slug') . '%');
        }
        if ($request->filled('cor') && \Illuminate\Support\Facades\Schema::hasColumn('situacoes_contratos', 'cor')) {
            $query->where('cor', 'like', '%' . $request->input('cor') . '%');
        }
        if ($request->filled('motivo') && \Illuminate\Support\Facades\Schema::hasColumn('situacoes_contratos', 'motivo')) {
            $query->where('motivo', 'like', '%' . $request->input('motivo') . '%');
        }

        $situacoes = $query->orderBy('nome')->paginate(20)->appends($request->query());

        return view('situacoes.index', compact('situacoes'));
    }

    public function listar(Request $request)
    {
        // Mantido por compatibilidade, porém index agora atende os filtros.
        return $this->index($request);
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
