<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateApfRequest;
use App\Models\Apf;
use App\Models\Projeto;

class ApfController extends Controller
{
    public function index(Projeto $projeto)
    {
        $apfs = $projeto->apfs()->latest()->paginate(20);

        return view('projetos.apf.index', compact('projeto', 'apfs'));
    }

    public function store(StoreApf $request, Projeto $projeto)
    {
        $data = $request->validated();
        $data['projeto_id'] = $projeto->id;

        Apf::create($data);

        return redirect()
            ->route('projetos.show', $projeto->id)
            ->with('success', 'APF adicionada.');
    }

    public function update(UpdateApfRequest $request, Projeto $projeto, Apf $apf)
    {
        $apf->update($request->validated());

        return redirect()
            ->route('projetos.show', $projeto->id)
            ->with('success', 'APF atualizada.');
    }

    public function destroy(Projeto $projeto, Apf $apf)
    {
        $apf->delete();

        return redirect()
            ->route('projetos.show', $projeto->id)
            ->with('success', 'APF removida.');
    }
}
