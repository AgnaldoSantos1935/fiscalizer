<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\Servidor;
use Illuminate\Http\Request;

class ServidorController extends Controller
{
    /**
     * Lista todos os servidores (AJAX ou view)
     */
    public function index(Request $request)
    {
        $query = Servidor::with('pessoa');

        if ($mat = trim((string) $request->get('matricula'))) {
            $query->where('matricula', 'like', "%{$mat}%");
        }
        if ($nome = trim((string) $request->get('nome'))) {
            $query->whereHas('pessoa', function ($q) use ($nome) {
                $q->where('nome_completo', 'like', "%{$nome}%");
            });
        }
        if ($cargo = trim((string) $request->get('cargo'))) {
            $query->where('cargo', 'like', "%{$cargo}%");
        }
        if ($lotacao = trim((string) $request->get('lotacao'))) {
            $query->where('lotacao', 'like', "%{$lotacao}%");
        }
        if ($vinculo = trim((string) $request->get('vinculo'))) {
            $query->where('vinculo', $vinculo);
        }
        if ($situacao = trim((string) $request->get('situacao'))) {
            $query->where('situacao', $situacao);
        }
        if ($admissaoIni = $request->get('admissao_ini')) {
            $query->whereDate('data_admissao', '>=', $admissaoIni);
        }
        if ($admissaoFim = $request->get('admissao_fim')) {
            $query->whereDate('data_admissao', '<=', $admissaoFim);
        }

        $situacoes = Servidor::select('situacao')->distinct()->pluck('situacao')->filter()->values();
        $vinculos = Servidor::select('vinculo')->distinct()->pluck('vinculo')->filter()->values();

        $servidores = $query->orderByDesc('id')->paginate(20)->appends($request->query());

        return view('servidores.index', compact('servidores', 'situacoes', 'vinculos'));
    }

    /**
     * Exibe formulário de criação
     */
    public function create()
    {
        $pessoas = Pessoa::orderBy('nome_completo')->get();

        return view('servidores.create', compact('pessoas'));
    }

    /**
     * Armazena novo servidor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
            'matricula' => 'required|string|max:50|unique:servidores,matricula',
            'cargo' => 'nullable|string|max:255',
            'funcao' => 'nullable|string|max:255',
            'lotacao' => 'nullable|string|max:255',
            'data_admissao' => 'nullable|date',
            'vinculo' => 'nullable|string|max:50',
            'situacao' => 'nullable|string|max:50',
            'salario' => 'nullable|numeric|min:0',
        ]);

        Servidor::create($validated);

        return redirect()->route('servidores.index')->with('success', 'Servidor cadastrado com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit(Servidor $servidor)
    {
        $pessoas = Pessoa::orderBy('nome_completo')->get();

        return view('servidores.edit', compact('servidor', 'pessoas'));
    }

    /**
     * Atualiza servidor
     */
    public function update(Request $request, Servidor $servidor)
    {
        $validated = $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
            'matricula' => 'required|string|max:50|unique:servidores,matricula,' . $servidor->id,
            'cargo' => 'nullable|string|max:255',
            'funcao' => 'nullable|string|max:255',
            'lotacao' => 'nullable|string|max:255',
            'data_admissao' => 'nullable|date',
            'vinculo' => 'nullable|string|max:50',
            'situacao' => 'nullable|string|max:50',
            'salario' => 'nullable|numeric|min:0',
        ]);

        $servidor->update($validated);

        return redirect()->route('servidores.index')->with('success', 'Servidor atualizado com sucesso!');
    }

    /**
     * Remove servidor
     */
    public function destroy(Servidor $servidor)
    {
        $servidor->delete();

        return redirect()->route('servidores.index')->with('success', 'Servidor excluído com sucesso!');
    }
}
