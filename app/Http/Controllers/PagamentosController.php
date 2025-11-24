<?php

namespace App\Http\Controllers;

class PagamentosController extends Controller
{
    public function create($empenhoId)
    {
        $empenho = \App\Models\Empenho::with(['empresa', 'contrato'])->findOrFail($empenhoId);

        return view('pagamentos.create', compact('empenho'));
    }

    public function store(\App\Http\Controllers\Requests\RegistrarPagamentoRequest $request, $empenhoId, \App\Services\FinanceiroService $financeiro)
    {
        $dados = $request->validated();
        // Upload opcional do comprovante
        if ($request->hasFile('arquivo_comprovante_pdf')) {
            $path = $request->file('arquivo_comprovante_pdf')->store('pagamentos_comprovantes', 'public');
            $dados['arquivo_comprovante_pdf'] = $path;
        }
        $pagamentoId = $financeiro->registrarPagamento($empenhoId, $dados, $request->user()->id);

        return redirect()->route('empenhos.show', $empenhoId)
            ->with('success', 'Pagamento registrado com sucesso.');
    }
}
