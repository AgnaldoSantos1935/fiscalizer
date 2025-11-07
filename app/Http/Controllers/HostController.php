<?php

namespace App\Http\Controllers;

use App\Models\Host;
use Illuminate\Http\Request;

class HostController extends Controller
{
    /**
     * 🔹 Retorna todos os hosts em formato JSON (para DataTables)
     */
public function getHostsJson(Request $request)
{
    $query = Host::with('escola:id_escola,escola,municipio');

    // 🔍 Filtros dinâmicos
    if ($request->filled('nome_conexao')) {
        $query->where('nome_conexao', 'like', '%' . $request->nome_conexao . '%');
    }

    if ($request->filled('provedor')) {
        $query->where('provedor', 'like', '%' . $request->provedor . '%');
    }

    if ($request->filled('tecnologia')) {
        $query->where('tecnologia', 'like', '%' . $request->tecnologia . '%');
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('municipio')) {
        $query->whereHas('escola', function ($q) use ($request) {
            $q->where('municipio', 'like', '%' . $request->municipio . '%');
        });
    }

    // 🔹 Ordena e obtém resultados
    $hosts = $query->orderBy('id', 'asc')->get();

    // 🔹 Formata os dados para o DataTable
    $data = $hosts->map(function ($h) {
        return [
            'id'            => $h->id,
            'nome_conexao'  => $h->nome_conexao,
            'descricao'     => $h->descricao,
            'provedor'      => $h->provedor,
            'tecnologia'    => $h->tecnologia,
            'ip_atingivel'  => $h->ip_atingivel,
            'status'        => $h->status,
            'nome_escola'   => $h->escola->escola ?? '—',
            'municipio'     => $h->escola->municipio ?? '—',
        ];
    });

    return response()->json(['data' => $data]);
}



    public function index()
    {

        return view('hosts.index');
    }


    /** 🆕 Exibe o formulário de criação */
    public function create()
    {
        return view('hosts.create');
    }

    /** 💾 Armazena novo host */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'tipo' => 'required|in:ip,link',
            'porta' => 'nullable|integer',
            'localizacao' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        Host::create($validated);

        return redirect()->route('hosts.index')->with('success', 'Host cadastrado com sucesso!');
    }

    /** ✏️ Editar */
    public function edit($id)
    {
        $host = Host::findOrFail($id);
        return view('hosts.edit', compact('host'));
    }

    /** 🔄 Atualizar */
    public function update(Request $request, $id)
    {
        $host = Host::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'tipo' => 'required|in:ip,link',
            'porta' => 'nullable|integer',
            'localizacao' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $host->update($validated);

        return redirect()->route('hosts.index')->with('success', 'Host atualizado com sucesso!');
    }

    /** 🗑️ Remover */
    public function destroy($id)
    {
        Host::findOrFail($id)->delete();
        return redirect()->route('hosts.index')->with('success', 'Host excluído com sucesso!');
    }

    /** 🔍 Exibir detalhes */
 public function show($id)
    {
        $host = Host::with(['escola:id_escola,escola,municipio'])
            ->find($id);

        if (!$host) {
            return response()->json(['error' => 'Host não encontrado.'], 404);
        }

        return response()->json($host);
    }

}
