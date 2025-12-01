<?php

namespace App\Services;

use App\Models\NormaTrecho;

class NormaTecnicaProcessorService
{
    public function indexarPdf(string $arquivoPdf, array $meta = []): int
    {
        $texto = '';
        try {
            $leitor = app(LeitorDocumentoService::class);
            $texto = $leitor->extrairPdf($arquivoPdf);
        } catch (\Throwable $e) {
            $texto = '';
        }

        $fonte = $meta['fonte'] ?? pathinfo($arquivoPdf, PATHINFO_FILENAME);
        $idioma = $meta['idioma'] ?? 'pt-BR';
        $tags = $meta['tags'] ?? [];

        $paragrafos = $this->segmentarParagrafos($texto);
        $emb = app(EmbeddingsService::class);

        $count = 0;
        foreach ($paragrafos as $idx => $p) {
            $p2 = trim($p);
            if ($p2 === '' || mb_strlen($p2) < 60) {
                continue;
            }
            $ref = $this->detectarReferencia($p2);
            $vec = $emb->embed($p2);
            NormaTrecho::create([
                'fonte' => $fonte,
                'referencia' => $ref,
                'idioma' => $idioma,
                'arquivo_pdf' => $arquivoPdf,
                'trecho_ordem' => $idx + 1,
                'trecho_texto' => $p2,
                'tags' => $tags,
                'embedding' => $vec,
            ]);
            $count++;
        }

        return $count;
    }

    private function segmentarParagrafos(string $texto): array
    {
        if ($texto === '') {
            return [];
        }
        $sep = preg_split('/\n{2,}|\r\n{2,}/', $texto);
        if ($sep && count($sep) > 1) {
            return $sep;
        }

        return preg_split('/(?<=[\.!?])\s+/u', $texto);
    }

    private function detectarReferencia(string $p): ?string
    {
        if (preg_match('/se(c|ç)ão\s+[\d\.]+/iu', $p, $m)) {
            return trim($m[0]);
        }
        if (preg_match('/cl(a|á)usula\s+[\d\.]+/iu', $p, $m)) {
            return trim($m[0]);
        }

        return null;
    }
}
