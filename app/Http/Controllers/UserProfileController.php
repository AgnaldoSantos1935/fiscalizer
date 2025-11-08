<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 Retorno AJAX (DataTables)
        if ($request->ajax()) {
            $query = UserProfile::with('user');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('acoes', function ($row) {
                    $btn = '
                        <a href="'.route('user_profiles.show', $row->id).'" class="btn btn-sm btn-info me-1">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="'.route('user_profiles.edit', $row->id).'" class="btn btn-sm btn-primary me-1">
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
    return view('user_profiles.create');
}

public function store(Request $request)
{
    $data = $request->validate([
        'user_id'             => 'nullable|exists:users,id',
        'nome_completo'       => 'required|string|max:255',
        'cpf'                 => 'required|string|max:14|unique:user_profiles',
        'rg'                  => 'nullable|string|max:20',
        'data_nascimento'     => 'nullable|date',
        'idade'               => 'nullable|integer',
        'sexo'                => 'nullable|string|max:20',
        'signo'               => 'nullable|string|max:20',
        'mae'                 => 'nullable|string|max:255',
        'pai'                 => 'nullable|string|max:255',
        'tipo_sanguineo'      => 'nullable|string|max:5',
        'altura'              => 'nullable|numeric',
        'peso'                => 'nullable|numeric',
        'cor_preferida'       => 'nullable|string|max:30',
        'cep'                 => 'nullable|string|max:10',
        'endereco'            => 'nullable|string|max:255',
        'numero'              => 'nullable|string|max:10',
        'bairro'              => 'nullable|string|max:255',
        'cidade'              => 'nullable|string|max:255',
        'estado'              => 'nullable|string|max:2',
        'telefone_fixo'       => 'nullable|string|max:20',
        'celular'             => 'nullable|string|max:20',
        'email_pessoal'       => 'nullable|email|max:255',
        'email_institucional' => 'nullable|email|max:255',
        'matricula'           => 'nullable|string|max:50',
        'cargo'               => 'nullable|string|max:100',
        'dre'                 => 'nullable|string|max:100',
        'lotacao'             => 'nullable|string|max:255',
        'foto'                => 'nullable|image|max:2048',
        'observacoes'         => 'nullable|string',
    ]);

    if ($request->hasFile('foto')) {
        $data['foto'] = $request->file('foto')->store('users', 'public');
    }

    $data['data_atualizacao'] = now();

    $profile = UserProfile::create($data);

    // Se o pedido veio via AJAX (ex: modal DataTables)
    if ($request->expectsJson()) {
        return response()->json(['success' => true, 'profile' => $profile]);
    }

    return redirect()->route('user_profiles.index')->with('success', 'Perfil criado com sucesso!');
}



    public function show($id)
    {
        $profile = UserProfile::with('user')->findOrFail($id);
        return view('user_profiles.show', compact('profile'));
    }

    public function edit($id)
    {
        $profile = UserProfile::with('user')->findOrFail($id);
        return view('user_profiles.edit', compact('profile'));
    }

    public function update(Request $request, $id)
    {
        $profile = UserProfile::findOrFail($id);

        $data = $request->validate([
            'nome_completo'       => 'required|string|max:255',
            'cpf'                 => 'nullable|string|max:14',
            'rg'                  => 'nullable|string|max:20',
            'data_nascimento'     => 'nullable|date',
            'idade'               => 'nullable|integer',
            'sexo'                => 'nullable|string|max:20',
            'signo'               => 'nullable|string|max:20',
            'mae'                 => 'nullable|string|max:255',
            'pai'                 => 'nullable|string|max:255',
            'tipo_sanguineo'      => 'nullable|string|max:5',
            'altura'              => 'nullable|numeric',
            'peso'                => 'nullable|numeric',
            'cor_preferida'       => 'nullable|string|max:30',
            'cep'                 => 'nullable|string|max:10',
            'endereco'            => 'nullable|string|max:255',
            'numero'              => 'nullable|string|max:10',
            'bairro'              => 'nullable|string|max:255',
            'cidade'              => 'nullable|string|max:255',
            'estado'              => 'nullable|string|max:2',
            'telefone_fixo'       => 'nullable|string|max:20',
            'celular'             => 'nullable|string|max:20',
            'email_pessoal'       => 'nullable|email|max:255',
            'email_institucional' => 'nullable|email|max:255',
            'matricula'           => 'nullable|string|max:50',
            'cargo'               => 'nullable|string|max:100',
            'dre'                 => 'nullable|string|max:100',
            'lotacao'             => 'nullable|string|max:255',
            'foto'                => 'nullable|image|max:2048',
            'observacoes'         => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            if ($profile->foto && Storage::disk('public')->exists($profile->foto)) {
                Storage::disk('public')->delete($profile->foto);
            }
            $data['foto'] = $request->file('foto')->store('users', 'public');
        }

        $data['data_atualizacao'] = now();
        $profile->update($data);

        return redirect()->route('user_profiles.index')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $profile = UserProfile::findOrFail($id);
        if ($profile->foto && Storage::disk('public')->exists($profile->foto)) {
            Storage::disk('public')->delete($profile->foto);
        }
        $profile->delete();

        return response()->json(['success' => true]);
    }
}

