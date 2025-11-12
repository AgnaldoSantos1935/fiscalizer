<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empenho;
use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\NotaEmpenhoItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class EmpenhoController extends Controller
{
    /** 🔹 DataTables AJAX */
    public function getData()
    {
        $query = Empenho::with(['empresa', 'contrato'])->select('empenhos.*');

        return DataTables::of($query)
            ->addColumn('empresa', fn($e) => $e->empresa->razao_social ?? '—')
            ->addColumn('contrato', fn($e) => $e->contrato->numero ?? '—')
            ->editColumn('valor_total', fn($e) => $e->valor_total_formatado)
            ->editColumn('data_lancamento', fn($e) => $e->data_formatada)
            ->addColumn('acoes', function ($e) {
                return '
                    <a href="' . route('empenhos.show', $e->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Visualizar">
                        <i class="fas fa-eye"></i>
                    </a>
                    <form action="' . route('empenhos.destroy', $e->id) . '" method="POST" class="d-inline">
                        ' . csrf_field() . method_field('DELETE') . '
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
        return view('empenhos.create', compact('empresas', 'contratos'));
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
            'itens.*.quantidade' => 'required|numeric|min:0',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $empenho = Empenho::create($validated);

            foreach ($request->itens ?? [] as $item) {
                $empenho->itens()->create([
                    'descricao' => $item['descricao'],
                    'quantidade' => $item['quantidade'],
                    'valor_unitario' => $item['valor_unitario'],
                    'valor_total' => $item['quantidade'] * $item['valor_unitario'],
                ]);
            }

            $empenho->update(['valor_total' => $empenho->itens()->sum('valor_total')]);
        });

        return redirect()->route('empenhos.index')->with('success', 'Nota de Empenho cadastrada com sucesso!');
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
    ->stream('Empenho_' . $nota->numero . '.pdf');


    }
}
