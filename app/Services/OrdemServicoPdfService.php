<?php

namespace App\Services;

use App\Models\Demanda;
use App\Models\DocumentoTecnico;
use App\Models\OrdemServico;
use Illuminate\Support\Facades\Storage;
use PDF;

class OrdemServicoPdfService
{
    public function gerarParaDemanda(Demanda $demanda, DocumentoTecnico $doc): OrdemServico
    {
        // Gera número da OS (ano + sequencial)
        $ano = now()->year;
        $sequencial = (OrdemServico::where('ano_os', $ano)->max('id') ?? 0) + 1;
        $numeroOs = str_pad($sequencial, 4, '0', STR_PAD_LEFT) . '/' . $ano;

        // Extrai dados do documento técnico
        $pfTotal = $doc->pf_estimado ?? 0;
        $ustTotal = $doc->ust_estimado ?? 0;
        $requisitos = $doc->requisitos_json ? json_decode($doc->requisitos_json, true) : [];
        $cronograma = $doc->cronograma_json ? json_decode($doc->cronograma_json, true) : [];

        // Cria registro da OS
        $os = OrdemServico::create([
            'demanda_id' => $demanda->id,
            'documento_tecnico_id' => $doc->id,
            'numero_os' => $numeroOs,
            'ano_os' => $ano,
            'pf_total' => $pfTotal,
            'ust_total' => $ustTotal,
            'cronograma_json' => $cronograma,
            'requisitos_json' => $requisitos,
            'data_emissao' => now(),
            'arquivo_pdf' => '', // preenche depois de salvar o arquivo
        ]);

        // Gera PDF usando Blade
        $pdf = PDF::loadView('pdf.ordem_servico', [
            'os' => $os,
            'demanda' => $demanda,
            'doc' => $doc,
            'requisitos' => $requisitos,
            'cronograma' => $cronograma,
        ])->setPaper('a4');

        $filePath = "ordens_servico/os_{$os->id}.pdf";
        Storage::disk('public')->put($filePath, $pdf->output());

        $os->arquivo_pdf = $filePath;
        $os->save();

        return $os;
    }
}
