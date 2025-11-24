<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class UserProfileController extends Controller
{
    public function me()
    {
        // Requer usuário autenticado
        if (! auth()->check()) {
            abort(403);
        }

        $userId = (int) auth()->id();
        $profile = UserProfile::with('user')->where('user_id', $userId)->first();

        if (! $profile) {
            // Se não houver perfil vinculado, direciona para criação
            return redirect()->route('user_profiles.create')
                ->with('warning', 'Seu perfil ainda não está configurado. Crie seu perfil.');
        }
        $isAdmin = auth()->user() && auth()->user()->can('view-index-user_profiles');

        return view('user_profiles.show', compact('profile', 'isAdmin'));
    }

    public function index(Request $request)
    {
        // Somente administradores podem listar perfis de usuários
        if (! auth()->user() || ! auth()->user()->can('view-index-user_profiles')) {
            abort(403);
        }
        // 🔹 Retorno AJAX (DataTables)
        if ($request->ajax()) {
            $query = UserProfile::with('user');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('acoes', function ($row) {
                    $btn = '
                        <a href="' . route('user_profiles.show', $row->id) . '" class="btn btn-sm btn-info me-1">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('user_profiles.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        </form>
                    ';

                    return $btn;
                })
                ->rawColumns(['acoes'])
                ->make(true);
        }

        // 🔹 Renderização normal (view)
        return view('user_profiles.index');
    }

    public function create()
    {
        // Somente administradores podem criar perfis
        if (! auth()->user() || ! auth()->user()->can('view-create-user_profiles')) {
            abort(403);
        }

        return view('user_profiles.create');
    }

    public function store(Request $request)
    {
        // Somente administradores podem criar perfis
        if (! auth()->user() || ! auth()->user()->can('view-create-user_profiles')) {
            abort(403);
        }
        // 🔹 Validação básica
        $data = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14|unique:user_profiles,cpf',
            'rg' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'idade' => 'nullable|integer',
            'sexo' => 'nullable|string|max:20',
            'mae' => 'nullable|string|max:255',
            'pai' => 'nullable|string|max:255',
            'tipo_sanguineo' => 'nullable|string|max:5',
            'altura' => 'nullable|string|max:5',
            'peso' => 'nullable|string|max:5',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'telefone_fixo' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'logradouro' => 'nullable|string|max:255',
            'complemento' => 'nullable|string|max:150',
            'matricula' => 'nullable|string|max:50',
            'cargo' => 'nullable|string|max:100',
            'dre' => 'nullable|string|max:100',
            'lotacao' => 'nullable|string|max:255',
            'foto' => 'nullable|image|max:2048',
            'observacoes' => 'nullable|string',
            // 🔐 Senha (opcional)
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $logradouro = trim($request->input('logradouro', ''));
        $numero = trim($request->input('numero', ''));
        $complemento = trim($request->input('complemento', ''));
        $bairro = trim($request->input('bairro', ''));
        if ($logradouro) {
            $parts = [];
            $parts[] = $logradouro;
            if ($numero) {
                $parts[] = $numero;
            }
            if ($complemento) {
                $parts[] = $complemento;
            }
            if ($bairro) {
                $parts[] = $bairro;
            }
            $data['endereco'] = implode(', ', $parts);
        }

        // 🔹 Normaliza altura/peso com ponto decimal
        $data['altura'] = isset($data['altura']) ? str_replace(',', '.', $data['altura']) : null;
        $data['peso'] = isset($data['peso']) ? str_replace(',', '.', $data['peso']) : null;

        // 🔹 Verifica e cria o usuário correspondente
        $email = $data['email'];

        $plainPassword = $request->input('password');
        $initialPassword = $plainPassword ?: Str::random(12);

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $data['nome_completo'],
                'password' => Hash::make($initialPassword), // senha definida ou temporária
                'role_id' => 2, // Ex: perfil "padrão"
            ]
        );

        // Caso o usuário já exista e uma senha tenha sido informada, atualiza a senha
        if (! $user->wasRecentlyCreated && $plainPassword) {
            $user->password = Hash::make($plainPassword);
            // mantém o nome atualizado
            $user->name = $data['nome_completo'];
            $user->save();
        }

        // 🔹 Upload da foto (opcional)
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('users', 'public');
        }

        // 🔹 Cria o perfil vinculado ao user_id
        $data['user_id'] = $user->id;
        $data['data_atualizacao'] = now();

        $profile = UserProfile::create($data);

        // 🔹 (Opcional) Envia link para definição da senha inicial
        // descomente se quiser enviar e-mail automático
        // Password::sendResetLink(['email' => $user->email]);

        // 🔹 Retorno padrão
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuário e perfil criados com sucesso!',
                'user' => $user,
                'profile' => $profile,
            ]);
        }

        return redirect()->route('user_profiles.index')->with('success', 'Usuário e perfil criados com sucesso!');
    }

    public function show($id)
    {
        $profile = UserProfile::with('user')->findOrFail($id);
        // Administradores podem ver qualquer perfil; outros apenas o próprio
        $isAdmin = auth()->user() && auth()->user()->can('view-index-user_profiles');
        if (! $isAdmin && (int) $profile->user_id !== (int) auth()->id()) {
            abort(403);
        }

        return view('user_profiles.show', compact('profile', 'isAdmin'));
    }

    public function edit($id)
    {
        $profile = UserProfile::with('user')->findOrFail($id);
        // Administradores podem editar qualquer perfil; outros apenas o próprio
        $isAdmin = auth()->user() && auth()->user()->can('view-index-user_profiles');
        if (! $isAdmin && (int) $profile->user_id !== (int) auth()->id()) {
            abort(403);
        }

        return view('user_profiles.edit', compact('profile', 'isAdmin'));
    }

    public function update(Request $request, $id)
    {
        $profile = UserProfile::findOrFail($id);
        // Administradores podem atualizar qualquer perfil; outros apenas o próprio
        $isAdmin = auth()->user() && auth()->user()->can('view-index-user_profiles');
        if (! $isAdmin && (int) $profile->user_id !== (int) auth()->id()) {
            abort(403);
        }

        // Validação conforme perfil de acesso (RCSB):
        // - Admin pode alterar todos os campos
        // - Não-admin só pode alterar a foto
        if ($isAdmin) {
            $data = $request->validate([
                'nome_completo' => 'required|string|max:255',
                'cpf' => 'nullable|string|max:14',
                'rg' => 'nullable|string|max:20',
                'data_nascimento' => 'nullable|date',
                'idade' => 'nullable|integer',
                'sexo' => 'nullable|string|max:20',
                'mae' => 'nullable|string|max:255',
                'pai' => 'nullable|string|max:255',
                'tipo_sanguineo' => 'nullable|string|max:5',
                'altura' => 'nullable|numeric',
                'peso' => 'nullable|numeric',
                'cep' => 'nullable|string|max:10',
                'endereco' => 'nullable|string|max:255',
                'numero' => 'nullable|string|max:10',
                'bairro' => 'nullable|string|max:255',
                'cidade' => 'nullable|string|max:255',
                'estado' => 'nullable|string|max:2',
                'telefone_fixo' => 'nullable|string|max:20',
                'celular' => 'nullable|string|max:20',
                'email' => 'required|email|max:255',
                'matricula' => 'nullable|string|max:50',
                'cargo' => 'nullable|string|max:100',
                'dre' => 'nullable|string|max:100',
                'lotacao' => 'nullable|string|max:255',
                'foto' => 'nullable|image|max:2048',
                'observacoes' => 'nullable|string',
                'logradouro' => 'nullable|string|max:255',
                'complemento' => 'nullable|string|max:150',
                // 🔐 Reset de senha (opcional)
                'password' => 'nullable|string|min:8|confirmed',
            ]);
        } else {
            $data = $request->validate([
                'foto' => 'nullable|image|max:2048',
            ]);
        }

        $logradouro = trim($request->input('logradouro', ''));
        $numero = trim($request->input('numero', ''));
        $complemento = trim($request->input('complemento', ''));
        $bairro = trim($request->input('bairro', ''));
        if ($logradouro) {
            $parts = [];
            $parts[] = $logradouro;
            if ($numero) {
                $parts[] = $numero;
            }
            if ($complemento) {
                $parts[] = $complemento;
            }
            if ($bairro) {
                $parts[] = $bairro;
            }
            $data['endereco'] = implode(', ', $parts);
        }

        if ($request->hasFile('foto')) {
            if ($profile->foto && Storage::disk('public')->exists($profile->foto)) {
                Storage::disk('public')->delete($profile->foto);
            }
            $data['foto'] = $request->file('foto')->store('users', 'public');
        }

        $data['data_atualizacao'] = now();
        $profile->update($data);

        // Mantém e-mail/nome sincronizados com o usuário somente para admin
        if ($isAdmin) {
            $user = $profile->user ?: User::find($profile->user_id);
            if ($user && isset($data['email'])) {
                $user->email = $data['email'];
                $user->name = $data['nome_completo'];
                $user->save();
            }
        }

        // 🔐 Reset de senha permitido apenas para admin
        if ($isAdmin && $request->filled('password')) {
            $user = $profile->user ?: User::find($profile->user_id);
            if ($user) {
                $user->password = Hash::make($request->input('password'));
                $user->name = $data['nome_completo'] ?? $user->name;
                $user->save();
            }
        }

        // Retorno adequado para chamadas AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso!',
                'profile_id' => $profile->id,
            ]);
        }

        return redirect()->route('user_profiles.show', $profile->id)->with('success', 'Perfil atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $profile = UserProfile::findOrFail($id);
        // Administradores podem excluir qualquer perfil; outros não podem excluir
        if (! auth()->user() || ! auth()->user()->can('view-index-user_profiles')) {
            abort(403);
        }
        if ($profile->foto && Storage::disk('public')->exists($profile->foto)) {
            Storage::disk('public')->delete($profile->foto);
        }
        $profile->delete();

        return response()->json(['success' => true]);
    }
}
