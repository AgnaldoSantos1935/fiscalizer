<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Empenho;
use App\Models\Contrato;
use App\Models\Empresa;

class EmpenhoController extends Controller
{
public function getData()
{
    $query = \App\Models\Empenho::with(['empresa', 'contrato'])->select('empenhos.*');

    return \Yajra\DataTables\Facades\DataTables::of($query)
        ->addColumn('empresa', fn($e) => $e->empresa->razao_social ?? '—')
        ->addColumn('contrato', fn($e) => $e->contrato->numero ?? '—')
        ->editColumn('valor_total', fn($e) => 'R$ ' . number_format($e->valor_total ?? 0, 2, ',', '.'))
        ->editColumn('data_lancamento', fn($e) => optional($e->data_lancamento)->format('d/m/Y') ?? '—')
        ->addColumn('acoes', function ($e) {
            return '
                <a href="'.route('empenhos.show', $e->id).'" class="btn btn-sm btn-outline-primary" title="Visualizar">
                    <i class="fas fa-eye"></i>
                </a>';
        })
        ->rawColumns(['acoes'])
        ->toJson();
}



    public function index()
    {
        $notas = Empenho::with('empresa', 'contrato')->orderByDesc('data_lancamento')->get();
        return view('empenhos.index', compact('notas'));
    }

    public function show($id)
    {
        $nota = Empenho::with('itens', 'empresa', 'contrato')->findOrFail($id);
        return view('empenhos.show', compact('nota'));
    }

    public function create()
    {
        $empresas = Empresa::orderBy('razao_social')->get();
        $contratos = Contrato::orderBy('numero')->get();
        return view('empenhos.create', compact('empresas', 'contratos'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'numero' => 'required|string|max:30|unique:empenhos,numero',
        'contrato_id' => 'required|exists:contratos,id',
        'data_lancamento' => 'nullable|date',
        'processo' => 'nullable|string|max:50',
        'programa_trabalho' => 'nullable|string|max:50',
        'fonte_recurso' => 'nullable|string|max:50',
        'natureza_despesa' => 'nullable|string|max:30',
        'valor_extenso' => 'nullable|string|max:255',
    ]);

    try {
        $empenho = Empenho::create($validated);

        // Itens
        if ($request->has('itens')) {
            foreach ($request->itens as $item) {
                $empenho->itens()->create($item);
            }
        }

        return redirect()->route('empenhos.index')
            ->with('success', 'Nota de Empenho cadastrada com sucesso!');
    } catch (\Throwable $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Erro ao salvar: ' . $e->getMessage());
    }
}




    public function destroy($id)
    {
        Empenho::findOrFail($id)->delete();
        return redirect()->route('empenhos.index')->with('success', 'Nota de Empenho removida.');
    }
    public function imprimir($id)
{
    $nota = Empenho::with(['itens', 'empresa', 'contrato'])->findOrFail($id);

    // Gera o PDF a partir da view já existente
    $pdf = Pdf::loadView('empenhos.pdf', compact('nota'))
        ->setPaper('a4', 'portrait');

    // Nome do arquivo
    $fileName = 'NotaEmpenho_' . $nota->numero . '.pdf';

    return $pdf->download($fileName);
}
}
