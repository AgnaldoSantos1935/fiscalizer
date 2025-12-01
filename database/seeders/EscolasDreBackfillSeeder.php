<?php

namespace Database\Seeders;

use App\Models\Dre;
use App\Models\Escola;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EscolasDreBackfillSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $path = base_path('database/data/escolas_dre.csv');
        if (! file_exists($path)) {
            $path = storage_path('app/seed/escolas_dre.csv');
        }
        if (! file_exists($path)) {
            if (isset($this->command)) {
                $this->command->warn('Arquivo de mapeamento não encontrado: database/data/escolas_dre.csv');
            }

            return;
        }

        $handle = fopen($path, 'r');
        if (! $handle) {
            return;
        }
        $firstLine = fgets($handle);
        $delimiter = $this->detectDelimiter($firstLine);
        $headers = array_map('trim', str_getcsv($firstLine, $delimiter));

        $updated = 0;
        $errors = 0;
        while (($line = fgets($handle)) !== false) {
            $cols = str_getcsv($line, $delimiter);
            if (count($cols) === 1 && trim($cols[0]) === '') {
                continue;
            }
            $row = [];
            foreach ($headers as $i => $h) {
                $row[$h] = $cols[$i] ?? null;
            }

            $codigo = trim((string) ($row['codigo_inep'] ?? ''));
            $nome = trim((string) ($row['escola'] ?? ($row['nome'] ?? '')));
            $municipio = trim((string) ($row['municipio'] ?? ($row['cidade'] ?? '')));
            $uf = strtoupper(trim((string) ($row['uf'] ?? '')));
            $dreVal = trim((string) ($row['dre'] ?? ($row['codigodre'] ?? ($row['dre_codigo'] ?? ''))));

            $dreCodigo = $this->resolveDreCodigo($dreVal);

            try {
                $escola = null;
                if ($codigo) {
                    $escola = Escola::where('codigo_inep', $codigo)->first();
                }
                if (! $escola && $nome) {
                    $q = Escola::where(function ($q) use ($nome) {
                        $q->where('escola', $nome)->orWhere('nome', $nome);
                    });
                    if ($municipio) {
                        $q->where('municipio', $municipio);
                    }
                    if ($uf) {
                        $q->where('uf', $uf);
                    }
                    $escola = $q->first();
                }
                if ($escola) {
                    $escola->update(['dre' => $dreCodigo]);
                    $updated++;
                }
            } catch (\Throwable $e) {
                $errors++;
            }
        }
        fclose($handle);

        if (isset($this->command)) {
            $this->command->info("Backfill: {$updated} escolas atualizadas, {$errors} erros");
        }
    }

    protected function detectDelimiter(string $line): string
    {
        $comma = substr_count($line, ',');
        $semicolon = substr_count($line, ';');
        $tab = substr_count($line, "\t");
        if ($tab > max($comma, $semicolon)) {
            return "\t";
        }

        return $semicolon > $comma ? ';' : ',';
    }

    protected function resolveDreCodigo(?string $val): ?string
    {
        $s = trim((string) $val);
        if ($s === '') {
            return null;
        }
        $sNorm = strtoupper($s);
        $dre = Dre::where('codigodre', $sNorm)->first();
        if ($dre) {
            return $dre->codigodre;
        }
        $dre2 = Dre::where('nome_dre', 'ilike', $s)->first();

        return $dre2 ? $dre2->codigodre : $sNorm;
    }
}
