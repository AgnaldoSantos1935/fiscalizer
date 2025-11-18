<?php

namespace App\Http\Controllers;

use App\Models\Empenho;
use Illuminate\Http\Request;

class EmpenhoItemController extends Controller
{
    public function index(Empenho $notaEmpenho)
    {
        $itens = $notaEmpenho->itens()->latest()->paginate(20);

        return view('empenhos.itens.index', compact('notaEmpenho', 'itens'));
    }

    public function store(Request $request, Empenho $notaEmpenho)
    {
        $data = $request->validate([
            'item_numero' => 'nullable|integer',
            'descricao' => 'required|string|max:500',
            'unidade' => 'nullable|string|max:50',
            'quantidade' => 'required|numeric|min:0',
            'valor_unitario' => 'required|numeric|min:0',
        ]);

        $data['nota_empenho_id'] = $notaEmpenho->id;
        EmpenhoItem::create($data);

        return back()->with('success', 'Item adicionado com sucesso!');
    }

    public function destroy(EmpenhoItem $item)
    {
        $item->delete();

        return back()->with('success', 'Item removido.');
    }
}
