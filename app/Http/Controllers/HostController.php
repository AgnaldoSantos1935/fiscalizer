<?php

namespace App\Http\Controllers;

use App\Models\Host;
use Illuminate\Http\Request;

class HostController extends Controller
{
    /** 🧩 Lista de hosts */
    public function index()
    {
        $hosts = Host::orderBy('nome')->get();
        return view('hosts.index', compact('hosts'));
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
        $host = Host::findOrFail($id);
        return view('hosts.show', compact('host'));
    }
}
