<?php

namespace App\Http\Controllers;

use App\Models\DRE;
use App\Models\Escola;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EscolaController extends Controller
{
    /**
     * Exibe a página principal com DataTable.
     */
    public function index()
    {
        return view('escolas.index');
    }

    /**
     * Endpoint para DataTables (JSON)
     */
    /* public function getData(Request $request)
     {
         if ($request->ajax()) {
             $escolas = Escola::select([
                 'id as id_escola',
                 'codigo_inep',
                 'escola',
                 'municipio',
                 'uf',
                 'dre',
                 'telefone'
             ]);

             return DataTables::of($escolas)
                 ->addColumn('dre_nome', fn($row) => $row->dre ?? '-')
                 ->make(true);
         }

         abort(404);
     }*/

    public function getData(Request $request)
    {
        $query = Escola::query();

        // 🔍 Filtros opcionais (compatíveis com UI)
        if ($request->filled('codigo')) {
            $query->where('codigo_inep', 'like', '%' . trim((string) $request->codigo) . '%');
        }
        if ($request->filled('nome')) {
            $query->where('escola', 'like', '%' . trim((string) $request->nome) . '%');
        }
        if ($request->filled('municipio')) {
            $query->where('municipio', 'like', '%' . trim((string) $request->municipio) . '%');
        }
        if ($request->filled('uf')) {
            $query->where('uf', strtoupper(trim((string) $request->uf)));
        }

        $escolas = $query
            ->select('id_escola', 'codigo_inep', 'escola', 'municipio', 'uf', 'dre')
            ->orderBy('escola')
            ->limit(500)
            ->get();

        return response()->json(['data' => $escolas]);
    }

    /**
     * Retorna JSON para o modal de detalhes (usado pelo fetch).
     */
    public function show($id_escola)
    {
        $escola = Escola::findOrFail($id_escola);

        return response()->json(['escola' => $escola]);
    }

    /**
     * Formulário de criação.
     */
    public function create()
    {
        $dres = DRE::all();

        return view('escolas.create', compact('dres'));
    }

    /**
     * Armazena nova escola.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_inep' => 'required|string|max:20|unique:escolas,codigo_inep',
            'escola' => 'required|string|max:255',
        ]);
        $logradouro = trim($request->input('logradouro', ''));
        $numero = trim($request->input('numero', ''));
        $complemento = trim($request->input('complemento', ''));
        $bairro = trim($request->input('bairro', ''));
        $endereco = $request->input('endereco');
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
            $endereco = implode(', ', $parts);
        }
        $data = $validated + $request->except('_token');
        if ($endereco) {
            $data['endereco'] = $endereco;
        }
        Escola::create($data);

        return redirect()->route('escolas.index')->with('success', 'Escola cadastrada com sucesso!');
    }

    /**
     * Formulário de edição.
     */
    public function edit(Escola $escola)
    {

        return view('escolas.edit', compact('escola'));
    }

    /**
     * Atualiza uma escola.
     */
    public function update(Request $request, Escola $escola)
    {
        $validated = $request->validate([
            'codigo_inep' => 'required|string|max:20|unique:escolas,codigo_inep,' . $escola->id,
            'escola' => 'required|string|max:255',
        ]);
        $logradouro = trim($request->input('logradouro', ''));
        $numero = trim($request->input('numero', ''));
        $complemento = trim($request->input('complemento', ''));
        $bairro = trim($request->input('bairro', ''));
        $endereco = $request->input('endereco');
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
            $endereco = implode(', ', $parts);
        }
        $data = $validated + $request->except(['_token', '_method']);
        if ($endereco) {
            $data['endereco'] = $endereco;
        }
        $escola->update($data);

        return redirect()->route('escolas.index')->with('success', 'Escola atualizada com sucesso!');
    }

    /**
     * Exclui uma escola.
     */
    public function destroy(Escola $escola)
    {
        $escola->delete();

        return response()->json(['success' => true]);
    }
}
