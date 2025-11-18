<?php

namespace App\Http\Controllers;

use App\Models\TermoReferencia;
use App\Models\TermoReferenciaItem;
use Illuminate\Http\Request;

class TermoReferenciaItemController extends Controller
{
    private function normalizeNumber($value)
    {
        if ($value === null) return null;
        $s = (string) $value;
        // remove separador de milhar e troca vírgula por ponto
        $s = str_replace(['.', ','], ['', '.'], $s);
        return is_numeric($s) ? $s : $value;
    }

    public function store(Request $request, TermoReferencia $tr)
    {
        if ($tr->status === 'finalizado') {
            return back()->with('error', 'Itens não podem ser alterados: TR finalizado.');
        }
        // normaliza números com máscara pt-BR
        $request->merge([
            'quantidade' => $this->normalizeNumber($request->input('quantidade')),
            'valor_unitario' => $this->normalizeNumber($request->input('valor_unitario')),
        ]);
        $data = $request->validate([
            'descricao'      => 'required|string|max:500',
            'unidade'        => 'nullable|string|max:50',
            'quantidade'     => 'required|numeric|min:0',
            'valor_unitario' => 'required|numeric|min:0',
        ]);

        $data['termo_referencia_id'] = $tr->id;
        TermoReferenciaItem::create($data);

        return back()->with('success', 'Item adicionado com sucesso!');
    }

    public function update(Request $request, TermoReferenciaItem $item)
    {
        $tr = $item->termoReferencia;
        if ($tr && $tr->status === 'finalizado') {
            return redirect()->route('contratacoes.termos-referencia.show', $tr)
                ->with('error', 'Itens não podem ser alterados: TR finalizado.');
        }
        $request->merge([
            'quantidade' => $this->normalizeNumber($request->input('quantidade')),
            'valor_unitario' => $this->normalizeNumber($request->input('valor_unitario')),
        ]);
        $data = $request->validate([
            'descricao'      => 'required|string|max:500',
            'unidade'        => 'nullable|string|max:50',
            'quantidade'     => 'required|numeric|min:0',
            'valor_unitario' => 'required|numeric|min:0',
        ]);

        $item->update($data);
        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Item atualizado com sucesso!');
    }

    public function destroy(TermoReferenciaItem $item)
    {
        $tr = $item->termoReferencia; // para redirecionar de volta
        if ($tr && $tr->status === 'finalizado') {
            return redirect()->route('contratacoes.termos-referencia.show', $tr)
                ->with('error', 'Itens não podem ser alterados: TR finalizado.');
        }
        $item->delete();
        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Item removido.');
    }
}