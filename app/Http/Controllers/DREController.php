<?php

namespace App\Http\Controllers;

use App\Models\Dre;
use Illuminate\Http\Request;

class DreController extends Controller
{
    /**
     * Lista todas as DREs.
     */
    public function index(Request $request)
    {
        $query = Dre::query();

        if ($codigo = trim((string) $request->get('codigo'))) {
            $query->where('codigodre', 'like', "%{$codigo}%");
        }
        if ($nome = trim((string) $request->get('nome'))) {
            $query->where('nome_dre', 'like', "%{$nome}%");
        }
        if ($municipio = trim((string) $request->get('municipio'))) {
            $query->where('municipio_sede', $municipio);
        }
        if ($email = trim((string) $request->get('email'))) {
            $query->where('email', 'like', "%{$email}%");
        }
        if ($uf = strtoupper((string) $request->get('uf'))) {
            if (strlen($uf) === 2) {
                $query->where('uf', $uf);
            }
        }
        if ($cep = preg_replace('/\D+/', '', (string) $request->get('cep'))) {
            if ($cep) {
                $query->where('cep', 'like', "%{$cep}%");
            }
        }

        $municipios = Dre::select('municipio_sede')->distinct()->orderBy('municipio_sede')->pluck('municipio_sede')->filter();
        $ufs = Dre::select('uf')->distinct()->orderBy('uf')->pluck('uf')->filter();

        $dres = $query->orderBy('nome_dre')->paginate(20)->appends($request->query());

        return view('dres.index', compact('dres', 'municipios', 'ufs'));
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create()
    {
        return view('dres.create');
    }

    /**
     * Armazena uma nova DRE no banco.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigodre' => 'required|string|max:10|unique:dres,codigodre',
            'nome_dre' => 'required|string|max:150',
            'municipio_sede' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'telefone' => 'nullable|string|max:50',
            'endereco' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:9',
            'logradouro' => 'nullable|string|max:200',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:150',
            'bairro' => 'nullable|string|max:100',
            'uf' => 'nullable|string|size:2',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
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
        $data = $request->all();
        $data['endereco'] = $endereco;
        Dre::create($data);

        return redirect()
            ->route('dres.index')
            ->with('success', 'DRE criada com sucesso.');
    }

    /**
     * Retorna uma DRE específica em JSON (para modais e fetch()).
     */
    public function show($id)
    {
        $dre = Dre::find($id);

        if (! $dre) {
            return response()->json(['error' => 'DRE não encontrada.'], 404);
        }

        return response()->json(['dre' => $dre]);
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit($id)
    {
        $dre = Dre::findOrFail($id);

        return view('dres.edit', compact('dre'));
    }

    /**
     * Atualiza uma DRE existente.
     */
    public function update(Request $request, $id)
    {
        $dre = Dre::findOrFail($id);

        $request->validate([
            'codigodre' => 'required|string|max:10|unique:dres,codigodre,' . $dre->id,
            'nome_dre' => 'required|string|max:150',
            'municipio_sede' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'telefone' => 'nullable|string|max:50',
            'endereco' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:9',
            'logradouro' => 'nullable|string|max:200',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:150',
            'bairro' => 'nullable|string|max:100',
            'uf' => 'nullable|string|size:2',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
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
        $data = $request->all();
        $data['endereco'] = $endereco;
        $dre->update($data);

        return redirect()
            ->route('dres.index')
            ->with('success', 'DRE atualizada com sucesso.');
    }

    /**
     * Remove uma DRE.
     */
    public function destroy($id)
    {
        $dre = \App\Models\Dre::find($id);

        if (! $dre) {
            return response()->json(['error' => 'DRE não encontrada.'], 404);
        }

        $dre->delete();

        return response()->json(['success' => true]);
    }
}
