<?php

namespace App\Http\Controllers;

use App\Models\OrdemFornecimento;
use Illuminate\Support\Facades\Storage;

class OrdemFornecimentoController extends Controller
{
    public function index()
    {
        $ofs = OrdemFornecimento::with(['contrato.contratada', 'empenho'])
            ->orderByDesc('id')->paginate(20);

        return view('ordens_fornecimento.index', compact('ofs'));
    }

    public function show(int $id)
    {
        $of = OrdemFornecimento::with(['contrato.contratada', 'empenho'])->findOrFail($id);

        return view('ordens_fornecimento.show', compact('of'));
    }

    public function pdf(int $id)
    {
        $of = OrdemFornecimento::findOrFail($id);
        if (! $of->arquivo_pdf || ! Storage::disk('public')->exists($of->arquivo_pdf)) {
            abort(404, 'PDF não encontrado para esta Ordem de Fornecimento.');
        }

        return Storage::disk('public')->download($of->arquivo_pdf, 'Ordem_de_Fornecimento_' . $of->numero_of . '.pdf');
    }
}
