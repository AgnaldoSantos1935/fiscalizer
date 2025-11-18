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
        'razao_social' => 'required|string|max:255',
        'cnpj' => 'required|string|max:20|unique:empresas,cnpj',
        'cep' => 'nullable|string|max:9',
        'logradouro' => 'nullable|string|max:255',
        'numero' => 'nullable|string|max:30',
        'complemento' => 'nullable|string|max:120',
        'bairro' => 'nullable|string|max:120',
        'cidade' => 'nullable|string|max:120',
        'uf' => 'nullable|string|max:2',
        ]);

    $payload = $validated + $request->except(['_token']);
    $payload['cnpj'] = preg_replace('/\D+/', '', (string) $payload['cnpj'] ?? '');
    if (!$this->isValidCnpj($payload['cnpj'])) {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'CNPJ inválido'], 422);
        }
        return back()->withErrors(['cnpj' => 'CNPJ inválido'])->withInput();
    }
    if (!empty($payload['cep'])) {
        $cepDigits = preg_replace('/\D+/', '', (string) $payload['cep']);
        $payload['cep'] = substr($cepDigits,0,8);
    }
    if (!empty($payload['uf'])) {
        $payload['uf'] = strtoupper($payload['uf']);
    }
    $empresa = Empresa::create($payload);

    if ($request->expectsJson()) {
        return response()->json(['success' => true, 'data' => $empresa]);
    }

    return redirect()->route('empresas.index')->with('success', 'Empresa cadastrada com sucesso!');
}

public function show(Empresa $empresa)
{
    if (request()->expectsJson()) {
        return response()->json(['empresa' => $empresa]);
    }
    return view('empresas.show', compact('empresa'));
}


    public function edit(Empresa $empresa)
{
    return view('empresas.edit', compact('empresa'));
}

public function update(Request $request, Empresa $empresa)
{
    $validated = $request->validate([
        'razao_social' => 'required|string|max:255',
        'cnpj' => 'required|string|max:20|unique:empresas,cnpj,' . $empresa->id,
        'cep' => 'nullable|string|max:9',
        'logradouro' => 'nullable|string|max:255',
        'numero' => 'nullable|string|max:30',
        'complemento' => 'nullable|string|max:120',
        'bairro' => 'nullable|string|max:120',
        'cidade' => 'nullable|string|max:120',
        'uf' => 'nullable|string|max:2',
    ]);

    $payload = $validated + $request->except(['_token', '_method']);
    $payload['cnpj'] = preg_replace('/\D+/', '', (string) $payload['cnpj'] ?? '');
    if (!$this->isValidCnpj($payload['cnpj'])) {
        return back()->withErrors(['cnpj' => 'CNPJ inválido'])->withInput();
    }
    if (!empty($payload['cep'])) {
        $cepDigits = preg_replace('/\D+/', '', (string) $payload['cep']);
        $payload['cep'] = substr($cepDigits,0,8);
    }
    if (!empty($payload['uf'])) {
        $payload['uf'] = strtoupper($payload['uf']);
    }

    $empresa->update($payload);
    return redirect()->route('empresas.index')->with('success', 'Dados atualizados com sucesso!');
}


    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('empresas.index')->with('success', 'Empresa excluída com sucesso!');
    }

    private function isValidCnpj(?string $cnpj): bool
    {
        if (!$cnpj || strlen($cnpj) !== 14) return false;
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) return false;
        $calc = function($base, $len){
            $sum = 0; $pos = $len - 7;
            for($i=0; $i<$len; $i++){
                $sum += intval($base[$i]) * $pos;
                $pos--; if($pos < 2) $pos = 9;
            }
            $res = $sum % 11; return ($res < 2) ? 0 : (11 - $res);
        };
        $d1 = $calc($cnpj, 12);
        if (intval($cnpj[12]) !== $d1) return false;
        $d2 = $calc($cnpj, 13);
        return intval($cnpj[13]) === $d2;
    }

    public function verificar(Request $r)
    {
        $cnpj = preg_replace('/\D+/', '', (string) $r->get('cnpj'));
        if (!$cnpj) {
            return response()->json(['found' => false, 'message' => 'CNPJ inválido'], 422);
        }
        $empresa = Empresa::where('cnpj', $cnpj)->first();
        if (!$empresa) {
            return response()->json(['found' => false]);
        }
        $contratosCount = \App\Models\Contrato::where('contratada_id', $empresa->id)->count();
        return response()->json([
            'found' => true,
            'data' => $empresa,
            'contratos_count' => $contratosCount,
        ]);
    }
}
