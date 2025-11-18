<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFiscalizacaoProjetoRequest;
use App\Http\Requests\UpdateFiscalizacaoProjetoRequest;
use App\Models\FiscalizacaoProjeto;
use App\Models\ProjetoSoftware;

class FiscalizacaoProjetoController extends Controller
{
    public function index(ProjetoSoftware $projeto)
    {
        $fiscalizacoes = $projeto->fiscalizacoes()->latest()->paginate(20);

        return view('fiscalizacoes.index', compact('projeto', 'fiscalizacoes'));
    }

    public function store(StoreFiscalizacaoProjetoRequest $request, ProjetoSoftware $projeto)
    {
        $data = $request->validated();
        $data['projeto_id'] = $projeto->id;
        FiscalizacaoProjeto::create($data);

        return back()->with('success', 'Fiscalização registrada.');
    }

    public function update(UpdateFiscalizacaoProjetoRequest $request, FiscalizacaoProjeto $fiscalizacao)
    {
        $fiscalizacao->update($request->validated());

        return back()->with('success', 'Fiscalização atualizada.');
    }

    public function destroy(FiscalizacaoProjeto $fiscalizacao)
    {
        $fiscalizacao->delete();

        return back()->with('success', 'Registro removido.');
    }
}
