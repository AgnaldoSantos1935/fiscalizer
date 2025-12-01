<?php

namespace Database\Seeders;

use App\Models\Dre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Smalot\PdfParser\Parser;

class DresPdfSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $path = env('DRES_PDF_PATH');
        if (! $path) {
            $candidate1 = storage_path('app/seed/ders.pdf');
            $candidate2 = base_path('database/data/ders.pdf');
            $path = file_exists($candidate1) ? $candidate1 : (file_exists($candidate2) ? $candidate2 : null);
        }
        if (! $path || ! file_exists($path)) {
            return;
        }

        $parser = new Parser;
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();
        $lines = preg_split('/\r?\n/', $text);

        $created = 0;
        $updated = 0;
        $current = null;
        $blocks = [];
        foreach ($lines as $line) {
            $s = trim($line);
            if ($s === '') {
                continue;
            }
            if (preg_match('/^(\S{2,})\s*[-–]\s*(.+)$/u', $s, $m) || preg_match('/^(\S{2,})\s+(.+)$/u', $s, $m)) {
                if ($current) {
                    $blocks[] = $current;
                }
                $current = ['code' => trim($m[1]), 'name' => trim($m[2]), 'lines' => []];

                continue;
            }
            if ($current) {
                $current['lines'][] = $s;
            }
        }
        if ($current) {
            $blocks[] = $current;
        }

        foreach ($blocks as $b) {
            $code = $b['code'];
            $name = $b['name'];
            $fields = $this->extractFields($b['lines']);
            $existing = Dre::where('codigodre', $code)->first();
            if ($existing) {
                $payload = array_merge(['nome_dre' => $name], $fields);
                if (! isset($payload['municipio_sede']) || $payload['municipio_sede'] === null) {
                    $payload['municipio_sede'] = $existing->municipio_sede ?: '';
                }
                $existing->fill($payload);
                $existing->save();
                $updated++;
            } else {
                $payload = array_merge(['codigodre' => $code, 'nome_dre' => $name], $fields);
                if (! isset($payload['municipio_sede']) || $payload['municipio_sede'] === null) {
                    $payload['municipio_sede'] = '';
                }
                Dre::create($payload);
                $created++;
            }
        }

        if (isset($this->command)) {
            $this->command->info("DREs: {$created} criados, {$updated} atualizados");
        }
    }

    protected function extractFields(array $lines): array
    {
        $email = null;
        $telefone = null;
        $cep = null;
        $uf = null;
        $municipio = null;
        $endereco = null;
        $logradouro = null;
        $numero = null;
        $complemento = null;
        $bairro = null;
        $ufs = '(AC|AL|AP|AM|BA|CE|DF|ES|GO|MA|MT|MS|MG|PA|PB|PR|PE|PI|RJ|RN|RS|RO|RR|SC|SP|SE|TO)';

        foreach ($lines as $s) {
            if (! $email && preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $s, $m)) {
                $email = $m[0];
            }
            if (preg_match_all('/\(\d{2}\)\s*\d{4,5}-\d{4}|\+?\d{2}\s*\d{2}\s*\d{4,5}-\d{4}/', $s, $mm)) {
                $t = implode(' / ', array_unique($mm[0]));
                $telefone = $telefone ? $telefone . ' / ' . $t : $t;
            }
            if (! $cep && preg_match('/\b\d{5}-?\d{3}\b/', $s, $m)) {
                $cep = $m[0];
            }
            if (! $uf && preg_match('/\b' . $ufs . '\b/u', $s, $m)) {
                $uf = strtoupper($m[0]);
            }
            if (! $municipio && preg_match('/([\pL .]+)\s*\/\s*' . $ufs . '/u', $s, $m)) {
                $municipio = trim($m[1]);
                $uf = strtoupper($m[2] ?? ($uf ?? ''));
            }
            if (! $endereco && (preg_match('/\bCEP\b/i', $s) || preg_match('/\b(RUA|AV\.?|AVENIDA|TRAVESSA|RODOVIA|ESTRADA)\b/i', $s))) {
                $endereco = $s;
            }
        }
        if ($endereco) {
            $tmp = $endereco;
            $tmp = preg_replace('/^\s*Endere[çc]o\s*[:\-]?\s*/i', '', $tmp);
            if ($cep) {
                $tmp = trim(str_replace($cep, '', $tmp));
            }
            if (preg_match('/^(.*?),\s*(\d+)\b/u', $tmp, $m)) {
                $logradouro = trim($m[1]);
                $numero = trim($m[2]);
            } else {
                $logradouro = $tmp;
            }
            if (preg_match('/\bBairro\b[:\s-]*([^\-\n]+)/iu', $tmp, $m)) {
                $bairro = trim($m[1]);
            }
        }

        return [
            'email' => $email,
            'telefone' => $telefone,
            'cep' => $cep,
            'uf' => $uf,
            'municipio_sede' => $municipio,
            'endereco' => $endereco,
            'logradouro' => $logradouro,
            'numero' => $numero,
            'complemento' => $complemento,
            'bairro' => $bairro,
        ];
    }
}
