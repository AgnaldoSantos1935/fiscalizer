<?php

namespace App\Services;

use App\Models\DocumentoTecnico;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DocumentoTecnicoIaService
{
    public function processarDocumento(DocumentoTecnico $doc): void
    {
        $arquivoPath = Storage::disk('public')->path($doc->arquivo_path);
        $base64 = base64_encode(file_get_contents($arquivoPath));

        // Envia para o WORKER Python
        $response = Http::timeout(120)
            ->post(config('services.ia_worker.url') . '/processar', [
                'file_base64' => $base64,
                'filename' => $doc->arquivo_original,
                'demanda_id' => $doc->demanda_id,
            ]);

        if (! $response->successful()) {
            $doc->status_validacao = 'invalido';
            $doc->inconsistencias_json = json_encode(['Falha ao processar documento.']);
            $doc->save();

            return;
        }

        $data = $response->json();

        $doc->requisitos_json = $data['requisitos'] ?? null;
        $doc->cronograma_json = $data['cronograma'] ?? null;
        $doc->telas_json = $data['telas'] ?? null;
        $doc->pf_estimado = $data['estimativas']['pf_total'] ?? 0;
        $doc->ust_estimado = $data['estimativas']['ust_total'] ?? 0;
        $doc->inconsistencias_json = $data['inconsistencias'] ?? [];
        $doc->resumo_ia_json = $data['resumo_ia'] ?? [];

        $doc->status_validacao =
            (empty($data['inconsistencias']) ? 'valido' : 'invalido');

        $doc->save();
    }
}
