<?php

namespace App\Services;

use App\Models\NormaTrecho;
use Illuminate\Support\Facades\DB;

class NormasRagService
{
    public function buscarFundamentacao(string $consulta, int $topK = 5, array $filtros = []): array
    {
        $emb = app(EmbeddingsService::class);
        $qvec = $emb->embed($consulta);

        $driver = null; try { $driver = DB::connection()->getDriverName(); } catch (\Throwable $e) {}
        $pref = config('rag.preferred_backend', 'auto');
        $usePg = ($driver === 'pgsql' && $this->hasPgVectorColumn() && $pref !== 'internal');
        if ($usePg) {
            $literal = '[' . implode(',', array_map(fn($v) => is_numeric($v) ? (string)$v : '0', $qvec)) . ']';
            $sql = 'SELECT fonte, referencia, trecho_texto, arquivo_pdf, (1 - (embedding_vec <-> :qvec::vector)) AS score FROM normas_trechos';
            $conds = [];
            $bind = ['qvec' => $literal];
            if (! empty($filtros['fonte'])) { $conds[] = 'fonte = :fonte'; $bind['fonte'] = $filtros['fonte']; }
            if (! empty($filtros['idioma'])) { $conds[] = 'idioma = :idioma'; $bind['idioma'] = $filtros['idioma']; }
            if (! empty($filtros['tags'])) {
                $tags = is_array($filtros['tags']) ? $filtros['tags'] : [$filtros['tags']];
                foreach ($tags as $i => $tg) { $conds[] = 'tags::jsonb @> :tag' . $i . '::jsonb'; $bind['tag' . $i] = json_encode([$tg]); }
            }
            if ($conds) { $sql .= ' WHERE ' . implode(' AND ', $conds); }
            $sql .= ' ORDER BY (embedding_vec <-> :qvec::vector) ASC LIMIT ' . intval($topK);
            $rows = DB::select($sql, $bind);
            return array_map(function($r){ return (array) $r; }, $rows);
        }

        $query = NormaTrecho::query();
        if (! empty($filtros['fonte'])) { $query->where('fonte', $filtros['fonte']); }
        if (! empty($filtros['idioma'])) { $query->where('idioma', $filtros['idioma']); }
        if (! empty($filtros['tags'])) {
            $tags = is_array($filtros['tags']) ? $filtros['tags'] : [$filtros['tags']];
            foreach ($tags as $tg) { $query->whereJsonContains('tags', $tg); }
        }

        $trechos = $query->limit(500)->get();
        $scored = [];
        foreach ($trechos as $t) {
            $tvec = is_array($t->embedding) ? $t->embedding : $emb->embed($t->trecho_texto);
            $score = $emb->cosine($qvec, $tvec);
            $scored[] = [
                'fonte' => $t->fonte,
                'referencia' => $t->referencia,
                'texto' => $t->trecho_texto,
                'arquivo_pdf' => $t->arquivo_pdf,
                'score' => $score,
            ];
        }
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($scored, 0, $topK);
    }

    public function justificar(string $afirmacao, array $filtros = []): array
    {
        $trechos = $this->buscarFundamentacao($afirmacao, 5, $filtros);

        $conclusao = $this->gerarConclusao($afirmacao, $trechos);
        return [
            'afirmacao' => $afirmacao,
            'trechos' => $trechos,
            'conclusao' => $conclusao,
        ];
    }

    private function gerarConclusao(string $afirmacao, array $trechos): string
    {
        if (empty($trechos)) { return 'Sem fundamentações indexadas para esta consulta.'; }
        $fonteTop = $trechos[0]['fonte'] ?? '';
        return 'Com base em ' . $fonteTop . ', recomenda-se: ' . $afirmacao;
    }

    private function hasPgVectorColumn(): bool
    {
        try {
            $rows = DB::select("SELECT 1 FROM information_schema.columns WHERE table_name = 'normas_trechos' AND column_name = 'embedding_vec' LIMIT 1");
            return !empty($rows);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
