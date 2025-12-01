<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Yaml\Yaml;

class ScrapController extends Controller
{
    public function index()
    {
        return view('scrap.test');
    }

    public function fetchSwagger(Request $request)
    {
        $url = $request->input('url');
        $force = filter_var($request->input('force', false), FILTER_VALIDATE_BOOLEAN);
        if (! $url) {
            return response()->json(['error' => 'URL não informada'], 422);
        }

        $specText = null;
        $cacheKey = 'scrap_swagger:' . sha1($url);
        if (! $force) {
            $specText = \Illuminate\Support\Facades\Cache::get($cacheKey);
        }
        if (! $specText) {
            $targetUrl = $url;
            if (! preg_match('/\.(json|yaml|yml)(\?.*)?$/i', $url)) {
                try {
                    $htmlResp = Http::timeout(20)->get($url);
                    if ($htmlResp->ok()) {
                        $html = $htmlResp->body();
                        $jsonLink = $this->extractOpenApiLink($html, $url);
                        if ($jsonLink) {
                            $targetUrl = $jsonLink;
                        } else {
                            $fallback = $this->guessOpenApiJson($url);
                            if ($fallback) {
                                $targetUrl = $fallback;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                }
            }
            try {
                $resp = Http::timeout(30)->get($targetUrl);
                if (! $resp->ok()) {
                    return response()->json(['error' => 'Falha ao baixar OpenAPI', 'status' => $resp->status()], 500);
                }
                $specText = $resp->body();
                \Illuminate\Support\Facades\Cache::put($cacheKey, $specText, now()->addMinutes(15));
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Erro de rede ao baixar OpenAPI', 'message' => $e->getMessage()], 500);
            }
        }

        $spec = null;
        $asJson = null;
        try {
            $asJson = json_decode($specText, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $asJson = null;
        }
        if (is_array($asJson)) {
            $spec = $asJson;
        } else {
            try {
                $spec = Yaml::parse($specText);
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Formato não suportado (use JSON ou YAML de OpenAPI/Swagger)'], 422);
            }
        }
        $paths = $spec['paths'] ?? [];
        $servers = $spec['servers'] ?? [];
        $info = $spec['info'] ?? [];
        $endpoints = [];
        foreach ($paths as $path => $ops) {
            foreach ($ops as $method => $meta) {
                $endpoints[] = [
                    'method' => strtoupper($method),
                    'path' => $path,
                    'summary' => $meta['summary'] ?? null,
                    'operationId' => $meta['operationId'] ?? null,
                    'tags' => $meta['tags'] ?? [],
                ];
            }
        }

        return response()->json([
            'info' => $info,
            'servers' => $servers,
            'count' => count($endpoints),
            'endpoints' => $endpoints,
        ]);
    }

    private function extractOpenApiLink(string $html, string $baseUrl): ?string
    {
        $candidates = [];
        preg_match_all('/<a[^>]*href=\"([^\"]+)\"[^>]*>(.*?)<\/a>/is', $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $href = $m[1] ?? '';
            $text = strip_tags($m[2] ?? '');
            if (preg_match('/swagger|openapi|api-docs/i', $text) || preg_match('/\.(json|yaml|yml)(\?.*)?$/i', $href)) {
                $candidates[] = $href;
            }
        }
        preg_match_all('/<script[^>]*src=\"([^\"]+)\"[^>]*>/is', $html, $scripts, PREG_SET_ORDER);
        foreach ($scripts as $s) {
            $src = $s[1] ?? '';
            if (preg_match('/swagger|openapi|api-docs/i', $src)) {
                $candidates[] = $src;
            }
        }
        if (empty($candidates)) {
            // Tenta extrair da configuração do SwaggerUIBundle
            if (preg_match('/SwaggerUIBundle\s*\(\s*\{[\s\S]*?url\s*:\s*\"([^\"]+)\"/is', $html, $m)) {
                $candidates[] = $m[1];
            } elseif (preg_match('/urls\s*:\s*\[\s*\{\s*url\s*:\s*\"([^\"]+)\"/is', $html, $m2)) {
                $candidates[] = $m2[1];
            }
        }
        if (empty($candidates)) {
            return null;
        }
        $href = $candidates[0];
        $p = parse_url($baseUrl);
        $scheme = $p['scheme'] ?? 'https';
        $host = $p['host'] ?? '';
        $basePath = rtrim(dirname($p['path'] ?? '/'), '/');
        if (preg_match('/^https?:\/\//i', $href)) {
            return $href;
        }
        if (str_starts_with($href, '/')) {
            return $scheme . '://' . $host . $href;
        }

        return $scheme . '://' . $host . $basePath . '/' . $href;
    }

    private function guessOpenApiJson(string $baseUrl): ?string
    {
        $p = parse_url($baseUrl);
        $scheme = $p['scheme'] ?? 'https';
        $host = $p['host'] ?? '';
        $path = $p['path'] ?? '/';
        $basePath = rtrim(dirname($path), '/');
        $parentPath = rtrim(dirname($basePath), '/');
        $cands = [
            '/v3/api-docs',
            '/swagger/v1/swagger.json',
            '/swagger.json',
            '/openapi.json',
        ];
        foreach ($cands as $c) {
            $u = $scheme . '://' . $host . $c;
            try {
                $r = Http::timeout(8)->head($u);
                if ($r->ok()) {
                    return $u;
                }
            } catch (\Throwable $e) {
            }
            $u2 = $scheme . '://' . $host . $basePath . $c;
            try {
                $r2 = Http::timeout(8)->head($u2);
                if ($r2->ok()) {
                    return $u2;
                }
            } catch (\Throwable $e) {
            }
            $u3 = $scheme . '://' . $host . $parentPath . $c;
            try {
                $r3 = Http::timeout(8)->head($u3);
                if ($r3->ok()) {
                    return $u3;
                }
            } catch (\Throwable $e) {
            }
            // Caso especial para base "/dados-abertos"
            $u4 = $scheme . '://' . $host . '/dados-abertos' . $c;
            try {
                $r4 = Http::timeout(8)->head($u4);
                if ($r4->ok()) {
                    return $u4;
                }
            } catch (\Throwable $e) {
            }
        }

        return null;
    }

    public function fetch(Request $request)
    {
        $url = $request->input('url');
        $delimiterOpt = $request->input('delimiter', ',');
        $delimiterCustom = (string) $request->input('delimiter_custom', '');
        $hasHeader = filter_var($request->input('has_header', true), FILTER_VALIDATE_BOOLEAN);
        $page = max(1, (int) ($request->input('page', 1)));
        $perPage = max(1, min(500, (int) ($request->input('per_page', 50))));
        $force = filter_var($request->input('force', false), FILTER_VALIDATE_BOOLEAN);
        $rawCsv = $request->input('raw_csv');
        $hasFile = $request->hasFile('csv') && $request->file('csv')->isValid();
        if (! $url && ! $hasFile && ! (is_string($rawCsv) && strlen($rawCsv) > 0)) {
            return response()->json(['error' => 'Origem não informada (URL ou arquivo CSV)'], 422);
        }

        $delimiter = $delimiterOpt === ';' ? ';' : ($delimiterOpt === 'tab' ? "\t" : ',');
        if ($delimiterOpt === 'custom' && strlen($delimiterCustom) >= 1) {
            $delimiter = mb_substr($delimiterCustom, 0, 1);
        }

        $csv = null;
        if ($hasFile) {
            try {
                $csv = file_get_contents($request->file('csv')->getRealPath());
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Falha ao ler arquivo enviado', 'message' => $e->getMessage()], 500);
            }
        } elseif (is_string($rawCsv) && strlen($rawCsv) > 0) {
            $csv = $rawCsv;
        } else {
            $cacheKey = 'scrap_csv:' . sha1($url);
            if (! $force) {
                $csv = Cache::get($cacheKey);
            }
            if (! $csv) {
                $targetUrl = $url;
                if (! preg_match('/\.csv(\?.*)?$/i', $url)) {
                    try {
                        $htmlResp = Http::timeout(20)->get($url);
                        if ($htmlResp->ok()) {
                            $html = $htmlResp->body();
                            $csvLink = $this->extractCsvLink($html, $url);
                            if ($csvLink) {
                                $targetUrl = $csvLink;
                            }
                        }
                    } catch (\Throwable $e) {
                    }
                }

                try {
                    $resp = Http::timeout(30)->get($targetUrl);
                    if (! $resp->ok()) {
                        return response()->json(['error' => 'Falha ao baixar CSV', 'status' => $resp->status()], 500);
                    }
                    $csv = $resp->body();
                    Cache::put($cacheKey, $csv, now()->addMinutes(15));
                } catch (\Throwable $e) {
                    return response()->json(['error' => 'Erro de rede ao baixar CSV', 'message' => $e->getMessage()], 500);
                }
            }
        }

        $lines = preg_split('/\r\n|\n|\r/', trim($csv));
        $rows = [];
        $issues = [];
        foreach ($lines as $i => $line) {
            $rows[] = str_getcsv($line, $delimiter);
        }
        if (empty($rows)) {
            return response()->json(['error' => 'CSV vazio'], 422);
        }
        $data = [];
        $headers = [];
        if ($hasHeader) {
            $headers = array_map(fn ($h) => trim((string) $h), $rows[0]);
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $item = [];
                foreach ($headers as $idx => $h) {
                    $item[$h ?: ('col_' . $idx)] = $row[$idx] ?? null;
                }
                if (count($row) !== count($headers)) {
                    $issues[] = [
                        'line' => $i + 1,
                        'expected_cols' => count($headers),
                        'found_cols' => count($row),
                    ];
                }
                $data[] = $item;
            }
        } else {
            $maxCols = 0;
            foreach ($rows as $r) {
                $maxCols = max($maxCols, count($r));
            }
            $headers = array_map(fn ($idx) => 'col_' . $idx, range(0, $maxCols - 1));
            foreach ($rows as $i => $row) {
                $item = [];
                foreach ($headers as $idx => $h) {
                    $item[$h] = $row[$idx] ?? null;
                }
                if (count($row) !== $maxCols) {
                    $issues[] = [
                        'line' => $i + 1,
                        'expected_cols' => $maxCols,
                        'found_cols' => count($row),
                    ];
                }
                $data[] = $item;
            }
        }

        $total = count($data);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);
        $offset = ($page - 1) * $perPage;
        $slice = array_slice($data, $offset, $perPage);

        return response()->json([
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => $pages,
            'headers' => $headers,
            'data' => $slice,
            'issues' => $issues,
        ]);
    }

    private function extractCsvLink(string $html, string $baseUrl): ?string
    {
        // Tenta achar link com textos comuns de exportação
        $candidates = [];
        preg_match_all('/<a[^>]*href=\"([^\"]+)\"[^>]*>(.*?)<\/a>/is', $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $href = $m[1] ?? '';
            $text = strip_tags($m[2] ?? '');
            if (preg_match('/export|exportar|csv/i', $text) || preg_match('/\.csv(\?.*)?$/i', $href)) {
                $candidates[] = $href;
            }
        }
        // Também busca botões
        preg_match_all('/<button[^>]*>(.*?)<\/button>/is', $html, $btns, PREG_SET_ORDER);
        foreach ($btns as $b) {
            $text = strip_tags($b[1] ?? '');
            if (preg_match('/export|exportar|csv/i', $text)) {
                // tentar achar um link na mesma região
                preg_match('/data-href=\"([^\"]+)\"/i', $html, $d);
                if (! empty($d[1])) {
                    $candidates[] = $d[1];
                }
            }
        }
        if (empty($candidates)) {
            return null;
        }
        $href = $candidates[0];
        // Resolve relativo
        if (preg_match('/^https?:\/\//i', $href)) {
            return $href;
        }
        $p = parse_url($baseUrl);
        $scheme = $p['scheme'] ?? 'https';
        $host = $p['host'] ?? '';
        $basePath = rtrim(dirname($p['path'] ?? '/'), '/');
        if (str_starts_with($href, '/')) {
            return $scheme . '://' . $host . $href;
        }

        return $scheme . '://' . $host . $basePath . '/' . $href;
    }
}
