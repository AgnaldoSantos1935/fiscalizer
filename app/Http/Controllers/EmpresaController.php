<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::all();
        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('empresas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'razao_social' => 'required|string|max:200',
            'cnpj' => 'required|string|max:14|unique:empresas',
            'email' => 'nullable|email|max:150',
            'telefone' => 'nullable|string|max:20',
        ]);

        Empresa::create($validated);
        return redirect()->route('empresas.index')->with('success', 'Empresa cadastrada com sucesso!');
    }
}
