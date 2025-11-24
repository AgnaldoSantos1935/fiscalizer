<?php

namespace App\Http\Controllers;

use App\Models\Host;
use Illuminate\Http\Request;

class HostController extends Controller
{
    public function index()
    {

        return view('hosts.index');
    }

    public function create()
    {
        return view('hosts.create');
    }

    public function getHostsJson(Request $request)
    {
        $query = Host::query();

        // 🔍 Filtros opcionais
        if ($request->filled('nome')) {
            $query->where('nome_conexao', 'like', '%' . trim((string) $request->nome) . '%');
        }
        if ($request->filled('provedor')) {
            $query->where('provedor', 'like', '%' . trim((string) $request->provedor) . '%');
        }
        if ($request->filled('tipo')) {
            $query->where('tipo_monitoramento', trim((string) $request->tipo));
        }

        $hosts = $query
            ->orderBy('nome_conexao')
            ->get([
                'id',
                'nome_conexao',
                'ip_atingivel',
                'porta',
                'provedor',
                'descricao',
                'tecnologia',
                'tipo_monitoramento',
                'status',
            ]);

        return response()->json([
            'data' => $hosts,   // <-- DataTables SÓ FUNCIONA com "data"
        ]);
    }

    public function store(Request $r)
    {
        $r->validate([
            'nome_conexao' => 'required|string|max:255',
            'host_alvo' => 'required',
            'tipo_monitoramento' => 'required|in:ping,porta,http,snmp,mikrotik,speedtest',
            'porta' => 'nullable|integer',
            'snmp_community' => 'nullable|string',
            'mikrotik_user' => 'nullable|string',
            'mikrotik_pass' => 'nullable|string',
            'config_extra' => 'nullable|json',
        ]);

        Host::create($r->all());

        return redirect()->route('hosts.index')
            ->with('success', 'Host cadastrado com sucesso!');
    }

    public function edit(Host $host)
    {
        return view('hosts.edit', compact('host'));
    }

    public function update(Request $r, Host $host)
    {
        $r->validate([
            'nome_conexao' => 'required',
            'host_alvo' => 'required',
            'tipo_monitoramento' => 'required',
            'config_extra' => 'nullable|json',
        ]);

        $host->update($r->all());

        return redirect()->route('hosts.index')
            ->with('success', 'Host atualizado com sucesso!');
    }

    public function show(Host $host)
    {
        return view('hosts.show', compact('host'));
    }

    public function status()
    {
        $status = Host::with(['monitoramentos' => function ($q) {
            $q->latest()->limit(1);
        }])
            ->get()
            ->map(function ($h) {
                return [
                    'id' => $h->id,
                    'status' => optional($h->monitoramentos->first())->online ? 1 : 0,
                ];
            });

        return response()->json($status);
    }
}
