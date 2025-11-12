<?php

namespace App\Http\Controllers;

use App\Models\Servidor;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ServidorController extends Controller
{
    /**
     * Lista todos os servidores (AJAX ou view)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Servidor::with('pessoa');

            return DataTables::of($query)
                ->addColumn('nome', fn($s) => $s->pessoa->nome_completo ?? '—')
                ->addColumn('matricula', fn($s) => $s->matricula ?? '—')
                ->addColumn('cargo', fn($s) => $s->cargo ?? '—')
                ->addColumn('situacao', fn($s) => ucfirst($s->situacao))
                ->addColumn('acoes', function ($s) {
                    return '
                        <a href="' . route('servidores.edit', $s->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="' . route('servidores.destroy', $s->id) . '" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Excluir este servidor?\')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>';
                })
                ->rawColumns(['acoes'])
                ->make(true);
        }

        return view('servidores.index');
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
