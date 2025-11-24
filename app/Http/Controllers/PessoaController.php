<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class PessoaController extends Controller
{
    /**
     * Lista todas as pessoas (AJAX/DataTables ou view)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Pessoa::with('user');

            return DataTables::of($query)
                ->addColumn('usuario', fn ($p) => $p->user ? $p->user->name : '—')
                ->addColumn('acoes', function ($p) {
                    return '
                        <a href="' . route('pessoas.edit', $p->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="' . route('pessoas.destroy', $p->id) . '" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Excluir esta pessoa?\')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>';
                })
                ->rawColumns(['acoes'])
                ->make(true);
        }

        return view('pessoas.index');
    }

    /**
     * Exibe formulário de criação
     */
    public function create()
    {
        return view('pessoas.create');
    }

    /**
     * Armazena uma nova pessoa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:pessoas,cpf',
            'email' => 'nullable|email|max:255|unique:users,email',
            'telefone' => 'nullable|string|max:20',
            'cep' => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'uf' => 'nullable|string|max:2',
        ]);

        // Cria o registro da pessoa
        $pessoa = Pessoa::create($validated);

        // RCSB: Criação/gestão de conta de usuário deve ser feita no módulo RCSB
        // Apenas administradores podem vincular/gerar conta de usuário a partir de Pessoa
        $isAdmin = auth()->user()?->hasRole('Administrador');
        if ($isAdmin && ! empty($validated['email'])) {
            $user = User::create([
                'name' => $validated['nome_completo'],
                'email' => $validated['email'],
                'password' => Hash::make('123456'), // senha padrão
            ]);

            $pessoa->update(['user_id' => $user->id]);
        }

        return redirect()->route('pessoas.index')->with('success', 'Pessoa cadastrada com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit(Pessoa $pessoa)
    {
        return view('pessoas.edit', compact('pessoa'));
    }

    /**
     * Atualiza dados da pessoa
     */
    public function update(Request $request, Pessoa $pessoa)
    {
        // Se o usuário tentar editar seus próprios dados via Pessoa (fora do RCSB)
        // e não for administrador, redireciona para "Meu Perfil"
        $isAdmin = auth()->user()?->hasRole('Administrador');
        if (! $isAdmin && auth()->id() && $pessoa->user_id === auth()->id()) {
            return redirect()
                ->route('user_profiles.me')
                ->with('info', 'A edição dos seus dados pessoais é centralizada no módulo RCSB. Utilize a página "Meu Perfil".');
        }

        $validated = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:pessoas,cpf,' . $pessoa->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . ($pessoa->user_id ?? 'NULL'),
            'telefone' => 'nullable|string|max:20',
            'cep' => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'uf' => 'nullable|string|max:2',
        ]);

        $pessoa->update($validated);

        // RCSB: Criação/gestão de conta de usuário deve ser feita no módulo RCSB
        // Apenas administradores podem vincular/atualizar conta de usuário a partir de Pessoa
        if ($isAdmin && ($validated['email'] ?? false)) {
            if ($pessoa->user) {
                $pessoa->user->update(['email' => $validated['email'], 'name' => $validated['nome_completo']]);
            } else {
                $user = User::create([
                    'name' => $validated['nome_completo'],
                    'email' => $validated['email'],
                    'password' => Hash::make('123456'),
                ]);
                $pessoa->update(['user_id' => $user->id]);
            }
        }

        return redirect()->route('pessoas.index')->with('success', 'Pessoa atualizada com sucesso!');
    }

    /**
     * Remove uma pessoa e o user vinculado (se existir)
     */
    public function destroy(Pessoa $pessoa)
    {
        // Apenas administradores podem excluir a conta de usuário vinculada
        $isAdmin = auth()->user()?->hasRole('Administrador');
        if ($isAdmin && $pessoa->user) {
            $pessoa->user->delete();
        }

        $pessoa->delete();

        return redirect()->route('pessoas.index')->with('success', 'Pessoa excluída com sucesso!');
    }
}
