<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Services\ContratoRiscoService;
use App\Services\IAContratoService;
use Illuminate\Http\Request;

class ContratoInteligenteController extends Controller
{
    public function uploadForm()
    {
        return view('contratos.upload');
    }

    public function receberUpload(Request $request, IAContratoService $ia, ContratoRiscoService $riscoService)
    {
        $request->validate([
            'arquivo' => 'required|file|max:50000',
        ]);

        $file = $request->file('arquivo');
        $path = $file->store('contratos/temp', 'public');

        // 1) IA extrai
        $resultado = $ia->processarContrato($path, $file->getClientOriginalName());
        $dadosContrato = $resultado['contrato'] ?? [];
        $inconsistenciasIa = $dadosContrato['inconsistencias'] ?? [];

        // 2) Cria um contrato "fantasma" só pra calcular risco (ainda não salva)
        $contrato = new \App\Models\Contrato;
        $contrato->fill([
            'numero' => $dadosContrato['numero'] ?? null,
            'processo_origem' => $dadosContrato['processo_origem'] ?? null,
            'modalidade' => $dadosContrato['modalidade'] ?? null,
            'objeto' => $dadosContrato['objeto'] ?? null,
            'objeto_resumido' => $dadosContrato['objeto_resumido'] ?? null,
            'valor_global' => $dadosContrato['valor_global'] ?? null,
            'valor_mensal' => $dadosContrato['valor_mensal'] ?? null,
            'quantidade_meses' => $dadosContrato['quantidade_meses'] ?? null,
            'data_assinatura' => $dadosContrato['data_assinatura'] ?? null,
            'data_inicio_vigencia' => $dadosContrato['data_inicio_vigencia'] ?? null,
            'data_fim_vigencia' => $dadosContrato['data_fim_vigencia'] ?? null,
            'clausulas' => isset($dadosContrato['clausulas'])
                                        ? json_encode($dadosContrato['clausulas'])
                                        : null,
        ]);

        // 3) Calcula risco
        $resultadoRisco = $riscoService->calcular($contrato, $inconsistenciasIa);

        // 4) Manda tudo pra view de pré-validação (usuário pode corrigir)
        return view('contratos.pre_validacao', [
            'dados' => $dadosContrato,
            'arquivo_path' => $path,
            'inconsistencias' => $inconsistenciasIa,
            'risco' => $resultadoRisco,
        ]);
    }

    public function salvar(Request $request)
    {
        $data = $request->except(['arquivo_path']);

        $contrato = Contrato::create($data);

        return redirect()
            ->route('contratos.show', $contrato->id)
            ->with('success', 'Contrato salvo com sucesso!');
    }
}
