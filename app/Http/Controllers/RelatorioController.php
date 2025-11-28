<?php

namespace App\Http\Controllers;

use App\Models\Relatorio;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        // Filtros dinâmicos
        $tipo = $request->input('tipo');
        $busca = $request->input('busca');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        $query = Relatorio::query();

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if ($busca) {
            $query->where('titulo', 'like', "%$busca%");
        }

        if ($dataInicio && $dataFim) {
            $query->whereBetween('created_at', [$dataInicio, $dataFim]);
        }

        $relatorios = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('relatorios.index', compact('relatorios', 'tipo', 'busca', 'dataInicio', 'dataFim'));
    }

    public function gerar(Request $request)
    {
        return view('relatorios.gerar');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'descricao' => 'nullable|string',
            'tipo' => 'required|string|max:50',
            'filtros' => 'nullable|array',
        ]);

        $data['user_id'] = auth()->id();

        Relatorio::create($data);

        return redirect()->route('relatorios.index')->with('success', 'Relatório salvo com sucesso!');
    }

    public function exportExcel(Request $request)
    {
        $tipo = $request->input('tipo');
        $busca = $request->input('busca');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        $query = Relatorio::with('user');

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if ($busca) {
            $query->where('titulo', 'like', "%$busca%");
        }

        if ($dataInicio && $dataFim) {
            $query->whereBetween('created_at', [$dataInicio, $dataFim]);
        }

        $relatorios = $query->orderBy('created_at', 'desc')->get();

        return Excel::download(new \App\Exports\RelatoriosExport($relatorios), 'relatorios.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tipo = $request->input('tipo');
        $busca = $request->input('busca');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        $query = Relatorio::with('user');

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if ($busca) {
            $query->where('titulo', 'like', "%$busca%");
        }

        if ($dataInicio && $dataFim) {
            $query->whereBetween('created_at', [$dataInicio, $dataFim]);
        }

        $relatorios = $query->orderBy('created_at', 'desc')->get();
        $pdf = PDF::loadView('relatorios.pdf', compact('relatorios'))->setPaper('a4', 'landscape');

        return $pdf->download('relatorios.pdf');
    }
}
