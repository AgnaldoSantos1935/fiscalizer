<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OcorrenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Ocorrencia::with('contrato');

        if ($tipo = trim((string) $request->get('tipo'))) {
            $query->where('tipo', 'like', "%{$tipo}%");
        }
        if ($contrato = trim((string) $request->get('contrato'))) {
            $query->whereHas('contrato', function ($q) use ($contrato) {
                $q->where('numero', 'like', "%{$contrato}%");
            });
        }

        $ocorrencias = $query->orderByDesc('id')->paginate(20)->appends($request->query());

        return view('ocorrencias.index', compact('ocorrencias'));
    }

    public function create()
    {
        // return view('ocorrencias.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
