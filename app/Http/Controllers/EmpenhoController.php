<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Empenho;
use App\Models\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EmpenhoController extends Controller
{
    /** 🔹 DataTables AJAX */
    public function getData()
    {
        $query = Empenho::with(['empresa', 'contrato'])->select('empenhos.*');

        return DataTables::of($query)
            ->addColumn('empresa', fn ($e) => $e->empresa->razao_social ?? '—')
            ->addColumn('contrato', fn ($e) => $e->contrato->numero ?? '—')
            ->editColumn('valor_total', fn ($e) => $e->valor_total_formatado)
            ->editColumn('data_lancamento', fn ($e) => $e->data_formatada)
            ->addColumn('acoes', function ($e) {
                return '
                    <a href="'.route('empenhos.show', $e->id).'" class="btn btn-sm btn-outline-primary me-1" title="Visualizar">
                        <i class="fas fa-eye"></i>
                    </a>
                    <form action="'.route('empenhos.destroy', $e->id).'" method="POST" class="d-inline">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm(\'Excluir empenho?\')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';
            })
            ->rawColumns(['acoes'])
            ->make(true);
    }

    /** 🔹 Lista inicial */
    public function index()
    {
        return view('empenhos.index');
    }

    /** 🔹 Exibição detalhada */
    public function show($id)
    {
        $nota = Empenho::with(['itens', 'empresa', 'contrato'])->findOrFail($id);

        return view('empenhos.show', compact('nota'));
    }

    /** 🔹 Formulário de cadastro */
    public function create()
    {
        $empresas = Empresa::orderBy('razao_social')->get();
        $contratos = Contrato::orderBy('numero')->get();
        $preContratoId = request('contrato_id');
        $preEmpresaId = null;
        $preContrato = null;
        if ($preContratoId) {
            $preContrato = Contrato::find($preContratoId);
            if ($preContrato) {
                $preEmpresaId = $preContrato->contratada_id;
            }
        }

        return view('empenhos.create', compact('empresas', 'contratos'))
            ->with('preContratoId', $preContratoId)
            ->with('preEmpresaId', $preEmpresaId)
            ->with('preContrato', $preContrato);
    }

    /** 🔹 Gravação */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:30|unique:empenhos,numero',
            'empresa_id' => 'required|exists:empresas,id',
            'contrato_id' => 'required|exists:contratos,id',
            'data_lancamento' => 'nullable|date',
            'processo' => 'nullable|string|max:50',
            'programa_trabalho' => 'nullable|string|max:50',
            'fonte_recurso' => 'nullable|string|max:50',
            'natureza_despesa' => 'nullable|string|max:30',
            'valor_extenso' => 'nullable|string|max:255',
            'itens.*.descricao' => 'required|string|max:255',
            'itens.*.quantidade' => 'required',
            'itens.*.valor_unitario' => 'required',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $empenho = Empenho::create($validated);

            foreach ($request->itens ?? [] as $item) {
                $qtd = $this->brToDecimal($item['quantidade']);
                $vu = $this->brToDecimal($item['valor_unitario']);
                $empenho->itens()->create([
                    'descricao' => $item['descricao'],
                    'quantidade' => $qtd,
                    'valor_unitario' => $vu,
                    'valor_total' => ($qtd ?? 0) * ($vu ?? 0),
                ]);
            }

            $empenho->update(['valor_total' => $empenho->itens()->sum('valor_total')]);
        });

        return redirect()->route('contratos.show', $validated['contrato_id'])
            ->with('success', 'Nota de Empenho cadastrada com sucesso!');
    }

    /** 🔹 Excluir */
    public function destroy($id)
    {
        Empenho::findOrFail($id)->delete();

        return back()->with('success', 'Nota de Empenho removida.');
    }

    /** 🔹 PDF */
    public function imprimir($id)
    {
        $nota = Empenho::with(['itens', 'empresa', 'contrato'])->findOrFail($id);
        $pdf = Pdf::loadView('empenhos.pdf', compact('nota'))->setPaper('a4', 'portrait');

        return Pdf::loadView('empenhos.pdf', compact('nota'))
            ->setPaper('a4', 'portrait')
            ->stream('Empenho_'.$nota->numero.'.pdf');

    }

    private function brToDecimal(?string $val): ?float
    {
        if ($val === null) {
            return null;
        }
        $clean = preg_replace('/[^\d,\.]/', '', $val);
        if ($clean === '' || $clean === null) {
            return null;
        }
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);

        return is_numeric($clean) ? (float) $clean : null;
    }
}
