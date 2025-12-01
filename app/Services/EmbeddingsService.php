<?php

namespace App\Services;

class EmbeddingsService
{
    public function embed(string $text, int $dims = 128): array
    {
        $tokens = preg_split('/[^\p{L}\p{N}\-\.]+/u', mb_strtolower($text));
        $vec = array_fill(0, $dims, 0.0);
        foreach ($tokens as $t) {
            if ($t === '' || mb_strlen($t) < 2) {
                continue;
            }
            $h = $this->hashToken($t) % $dims;
            $vec[$h] += 1.0;
        }
        $norm = $this->norm($vec);
        if ($norm > 0) {
            foreach ($vec as $i => $v) {
                $vec[$i] = $v / $norm;
            }
        }

        return $vec;
    }

    public function cosine(array $a, array $b): float
    {
        $sum = 0.0;
        $na = 0.0;
        $nb = 0.0;
        $len = min(count($a), count($b));
        for ($i = 0; $i < $len; $i++) {
            $sum += $a[$i] * $b[$i];
            $na += $a[$i] * $a[$i];
            $nb += $b[$i] * $b[$i];
        }
        if ($na == 0.0 || $nb == 0.0) {
            return 0.0;
        }

        return $sum / (sqrt($na) * sqrt($nb));
    }

    private function hashToken(string $t): int
    {
        return intval(sprintf('%u', crc32($t)));
    }

    private function norm(array $v): float
    {
        $s = 0.0;
        foreach ($v as $x) {
            $s += $x * $x;
        }

return sqrt($s);
    }
}
