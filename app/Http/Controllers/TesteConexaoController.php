<?php

namespace App\Http\Controllers;

use App\Models\TesteConexao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TesteConexaoController extends Controller
{
    /**
     * Exibe a página de testes de IP/Domínio
     */
    public function index()
    {
        $teste = TesteConexao::orderBy('nome')->get();

        return view('monitoramentos.teste', compact('teste'));
    }

    public function testar(Request $request)
    {
        $request->validate([
            'alvo' => 'required|string|max:255',
        ]);

        $entrada = trim($request->input('alvo'));
        $alvo = preg_replace(['#^https?://#', '#/$#'], '', $entrada);

        $dados = [
            'alvo' => $alvo,
            'tipo' => filter_var($alvo, FILTER_VALIDATE_IP) ? 'IP' : 'Domínio',
            'dns' => null,
            'ping' => '—',
            'http_status' => null,
            'http_ok' => false,
            'tempo_resposta' => null,
            'http_erro' => null,
        ];

        // DNS, ping e HTTP (mesmo código anterior)
        // ...

        $monitoramentos = Monitoramento::orderBy('nome')->get();

        return view('monitoramentos.teste', compact('dados', 'monitoramentos'));
    }

    public function create()
    {
        return view('monitoramentos.create');
    }

    /**
     * Armazena um novo registro
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:150',
            'tipo' => 'required|in:ip,link',
            'alvo' => 'required|string|max:255',
            'porta' => 'nullable|integer',
            'ativo' => 'boolean',
        ]);

        Monitoramento::create($validated);

        return redirect()->route('monitoramentos.index')
            ->with('success', 'Monitoramento cadastrado com sucesso!');
    }

    /**
     * Testa e atualiza o status de um monitoramento específico.
     */

    /**
     * Atualiza manualmente um monitoramento (editar)
     */
    public function edit($id)
    {
        $monitoramento = Monitoramento::findOrFail($id);

        return view('monitoramentos.edit', compact('monitoramento'));
    }

    public function update(Request $request, $id)
    {
        $monitoramento = Monitoramento::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:150',
            'tipo' => 'required|in:ip,link',
            'alvo' => 'required|string|max:255',
            'porta' => 'nullable|integer',
            'ativo' => 'boolean',
        ]);

        $monitoramento->update($validated);

        return redirect()->route('monitoramentos.index')
            ->with('success', 'Monitoramento atualizado com sucesso!');
    }

    public function destroy($id)
    {
        Monitoramento::findOrFail($id)->delete();

        return redirect()->route('monitoramentos.index')
            ->with('success', 'Monitoramento removido!');
    }

    public function historico($id)
    {
        $monitoramento = \App\Models\Monitoramento::findOrFail($id);

        $logs = \App\Models\MonitoramentoLog::where('monitoramento_id', $id)
            ->orderByDesc('data_teste')
            ->take(50)
            ->get();

        // Cálculos simples
        $total = $logs->count();
        $online = $logs->where('online', true)->count();
        $uptime = $total ? round(($online / $total) * 100, 2) : 0;
        $mediaLatencia = $logs->whereNotNull('latencia')->avg('latencia');

        return view('monitoramentos.historico', compact('monitoramento', 'logs', 'uptime', 'mediaLatencia'));
    }

    public function show($id)
    {
        $monitoramentos = \App\Models\Monitoramento::findOrFail($id);

        return view('monitoramentos.show', compact('monitoramentos'));
    }
}
