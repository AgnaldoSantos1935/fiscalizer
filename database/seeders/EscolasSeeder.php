<?php

namespace Database\Seeders;

use App\Models\Dre;
use App\Models\Escola;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EscolasSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $path = base_path('database/seeders/data/escolas.json');
        if (! file_exists($path)) {
            if (isset($this->command)) {
                $this->command->warn('Arquivo não encontrado: database/seeders/data/escolas.json');
            }

            return;
        }

        $raw = file_get_contents($path);
        $raw = preg_replace('/\bNaN\b/i', 'null', $raw);
        $data = json_decode($raw, true);
        if (! is_array($data)) {
            if (isset($this->command)) {
                $this->command->error('JSON inválido em escolas.json');
            }

            return;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $nameColumn = Schema::hasColumn('escolas', 'escola') ? 'escola' : (Schema::hasColumn('escolas', 'nome') ? 'nome' : null);
        $hasCodigoCol = Schema::hasColumn('escolas', 'codigo');
        foreach ($data as $row) {
            $nome = $this->normalizeLabel($row['Escola'] ?? null);
            $codigo = $this->normalizeInep($row['Código INEP'] ?? null);
            $uf = strtoupper(trim((string) ($row['UF'] ?? '')));
            $municipio = $this->normalizeLabel($row['Município'] ?? null);
            $localizacao = $this->normalizeLabel($row['Localização'] ?? null);
            $endereco = $this->normalizeAddress($row['Endereço'] ?? null);
            $cep = $this->extractCep($row['Endereço'] ?? null);
            $telefone = $this->normalizePhone($row['Telefone'] ?? null);
            $lat = $this->normalizeNumber($row['Latitude'] ?? null);
            $lng = $this->normalizeNumber($row['Longitude'] ?? null);

            if (! $nome || (! $codigo && ! $municipio)) {
                $skipped++;

                continue;
            }

            $dreCodigo = $this->resolveDreByMunicipio($municipio);

            $payload = [
                'codigo_inep' => $codigo,
                'uf' => $uf ?: null,
                'municipio' => $municipio,
                'localizacao' => $localizacao,
                'endereco' => $endereco,
                'cep' => $cep,
                'telefone' => $telefone,
                'latitude' => $lat,
                'longitude' => $lng,
                'dre' => $dreCodigo,
            ];
            if ($nameColumn) {
                $payload[$nameColumn] = $nome;
            }
            if ($hasCodigoCol) {
                $payload['codigo'] = $codigo ?? $this->makeCodigo($nome, $municipio);
            }

            if ($codigo) {
                $existing = Escola::where('codigo_inep', $codigo)->first();
            } else {
                $existing = Escola::where($nameColumn ?: 'escola', $nome)
                    ->where('municipio', $municipio)
                    ->first();
            }

            if ($existing) {
                $existing->update($payload);
                $updated++;
            } else {
                Escola::create($payload);
                $created++;
            }
        }

        if (isset($this->command)) {
            $this->command->info("Escolas: criadas {$created}, atualizadas {$updated}, ignoradas {$skipped}");
        }
    }

    private function normalizeLabel($val): ?string
    {
        $s = trim((string) ($val ?? ''));
        if ($s === '') {
            return null;
        }
        $s = str_replace(['—', '–', '‑', '−'], '-', $s);
        $s = preg_replace('/\s*-\s*/u', '-', $s);
        $s = preg_replace('/\s+/u', ' ', $s);

        return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
    }

    private function normalizeAddress($val): ?string
    {
        $s = trim((string) ($val ?? ''));
        if ($s === '' || $s === 'NA') {
            return null;
        }
        $s = preg_replace('/\s+/u', ' ', $s);

        return $s;
    }

    private function extractCep($val): ?string
    {
        $s = (string) ($val ?? '');
        if ($s === '') {
            return null;
        }
        if (preg_match('/(\d{5}-\d{3})/', $s, $m)) {
            return $m[1];
        }

        return null;
    }

    private function normalizePhone($val): ?string
    {
        $s = trim((string) ($val ?? ''));
        if ($s === '' || strtolower($s) === 'nan') {
            return null;
        }

        return $s;
    }

    private function normalizeInep($val): ?string
    {
        if ($val === null) {
            return null;
        }
        $s = trim((string) $val);

        return $s !== '' ? $s : null;
    }

    private function normalizeNumber($val): ?float
    {
        if ($val === null) {
            return null;
        }
        $s = trim((string) $val);
        if ($s === '' || strtolower($s) === 'nan') {
            return null;
        }

        return is_numeric($s) ? (float) $s : null;
    }

    private function resolveDreByMunicipio(?string $municipio): ?string
    {
        $m = trim((string) ($municipio ?? ''));
        if ($m === '') {
            return null;
        }
        $dre = Dre::where('municipio_sede', 'ilike', $m)->first();

        return $dre ? $dre->codigodre : null;
    }

    private function makeCodigo(?string $nome, ?string $municipio): string
    {
        $base = trim(($nome ?? '')) . '|' . trim(($municipio ?? ''));

        return strtoupper('ESC-' . substr(sha1($base), 0, 8));
    }
}
