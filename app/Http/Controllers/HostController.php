<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\Contrato;
use App\Models\ContratoItem;
use App\Models\Escola;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HostController extends Controller
{
    /**
     * Listagem principal (DataTables + filtros)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $hosts = Host::with(['itemContrato.contrato', 'escola'])
                ->when($request->contrato_id, function ($q) use ($request) {
                    $q->whereHas('itemContrato.contrato', function ($query) use ($request) {
                        $query->where('id', $request->contrato_id);
                    });
                })
                ->when($request->itemcontratado, function ($q) use ($request) {
                    $q->where('itemcontratado', $request->itemcontratado);
                })
                ->when($request->provedor, function ($q) use ($request) {
                    $q->where('provedor', 'like', "%{$request->provedor}%");
                })
                ->when($request->status, function ($q) use ($request) {
                    $q->where('status', $request->status);
                });

            return DataTables::of($hosts)
                ->addIndexColumn()
                ->addColumn('contrato', fn($row) => optional($row->itemContrato->contrato)->numero ?? '—')
                ->addColumn('item', fn($row) => $row->itemContrato->descricao_item ?? '—')
                ->addColumn('escola', fn($row) => $row->escola->escola ?? '—')
                ->addColumn('acoes', function ($row) {
                    return '
                        <a href="' . route('hosts.show', $row->id) . '"
                           class="btn btn-sm btn-info me-1" title="Ver Detalhes">
                            <i class="fas fa-eye"></i>
                        </a>
                    ';
                })
                ->rawColumns(['acoes'])
                ->make(true);
        }

        $contratos = Contrato::orderBy('numero')->get();
        return view('hosts.index', compact('contratos'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $contratos = Contrato::orderBy('numero')->get();
        $escolas = Escola::orderBy('escola')->get();
        return view('hosts.create', compact('contratos', 'escolas'));
    }

    /**
     * Armazena novo registro
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_conexao' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:255',
            'provedor' => 'nullable|string|max:100',
            'tecnologia' => 'nullable|string|max:50',
            'ip_atingivel' => 'nullable|string|max:45',
            'porta' => 'nullable|integer',
            'status' => 'required|in:ativo,inativo,em manutenção',
            'local' => 'nullable|integer|exists:escolas,id_escola',
            'itemcontratado' => 'nullable|integer|exists:contrato_itens,id',
        ]);

        Host::create($data);

        return redirect()->route('hosts.index')
            ->with('success', 'Conexão de rede criada com sucesso!');
    }

    /**
     * Exibe detalhes
     */
    public function show($id)
    {
        $host = Host::with(['itemContrato.contrato', 'escola'])->findOrFail($id);
        return view('hosts.show', compact('host'));
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $host = Host::findOrFail($id);
        $contratos = Contrato::orderBy('numero')->get();
        $itens = ContratoItem::where('contrato_id', optional($host->itemContrato->contrato)->id)->get();
        $escolas = Escola::orderBy('escola')->get();

        return view('hosts.edit', compact('host', 'contratos', 'itens', 'escolas'));
    }

    /**
     * Atualiza registro
     */
    public function update(Request $request, $id)
    {
        $host = Host::findOrFail($id);

        $data = $request->validate([
            'nome_conexao' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:255',
            'provedor' => 'nullable|string|max:100',
            'tecnologia' => 'nullable|string|max:50',
            'ip_atingivel' => 'nullable|string|max:45',
            'porta' => 'nullable|integer',
            'status' => 'required|in:ativo,inativo,em manutenção',
            'local' => 'nullable|integer|exists:escolas,id_escola',
            'itemcontratado' => 'nullable|integer|exists:contrato_itens,id',
        ]);

        $host->update($data);

        return redirect()->route('hosts.index')
            ->with('success', 'Conexão atualizada com sucesso!');
    }

    /**
     * Remove registro
     */
    public function destroy($id)
    {
        $host = Host::findOrFail($id);
        $host->delete();

        return response()->json(['success' => true]);
    }

    /**
     * API: Retorna itens do contrato selecionado (para selects dinâmicos)
     */
    public function getItensPorContrato($contratoId)
    {
        $itens = ContratoItem::where('id', $contratoId)
            ->orderBy('descricao_item')
            ->get(['id', 'descricao_item']);

        return response()->json($itens);
    }
}
