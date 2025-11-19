<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MapaController extends Controller
{
    public function index()
    {
        // Lista de DREs disponíveis para filtro (normalização de label e ordenação acento-insensível)
        $dresRaw = DB::table('dres')
            ->select('id', 'nome_dre')
            ->whereNotNull('nome_dre')
            ->get();

        $dres = collect($dresRaw)
            ->map(function ($d) {
                $s = is_string($d->nome_dre) ? trim($d->nome_dre) : '';
                // normaliza diferentes traços para hífen ASCII
                $s = str_replace(["—","–","‑","−"], "-", $s);
                // remove espaços ao redor do hífen
                $s = preg_replace('/\s*-\s*/u', '-', $s);
                // colapsa múltiplos espaços
                $s = preg_replace('/\s+/u', ' ', $s);

                // label em Title Case
                $label = mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
                // key para ordenação acento-insensível
                $trans = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
                $key = strtolower(trim($trans));
                return ['id' => $d->id, 'label' => $label, 'key' => $key];
            })
            ->filter(fn($it) => ! empty($it['label']))
            ->unique('id')
            ->sortBy('key')
            ->values();

        // Detecta colunas disponíveis na tabela 'escolas' para montar filtros dinâmicos
        $cols = Schema::getColumnListing('escolas');
        $lcMap = [];
        foreach ($cols as $c) { $lcMap[strtolower($c)] = $c; }
        $find = function (array $cands) use ($lcMap) {
            foreach ($cands as $cand) { $k = strtolower($cand); if (isset($lcMap[$k])) return $lcMap[$k]; }
            return null;
        };

        $municipioCol = $find(['municipio']);
        $dependenciaCol = $find(['dependencia_administrativa', 'dependencia']);

        $municipios = collect();
        if ($municipioCol) {
            $municipiosRaw = DB::table('escolas')
                ->select($municipioCol)
                ->whereNotNull($municipioCol)
                ->distinct()
                ->pluck($municipioCol);

            // Normaliza nomes de municípios: colapsa espaços, normaliza hífens, trim; gera label e key
            $municipios = collect($municipiosRaw)
                ->map(function ($m) {
                    if (! is_string($m)) return null;

                    $s = trim($m);
                    // normaliza diferentes traços para hífen ASCII
                    $s = str_replace(["—","–","‑","−"], "-", $s);
                    // remove espaços ao redor do hífen
                    $s = preg_replace('/\s*-\s*/u', '-', $s);
                    // colapsa múltiplos espaços
                    $s = preg_replace('/\s+/u', ' ', $s);

                    // label em Title Case (mantendo acentos)
                    $label = mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');

                    // key: lowercase, colapsa espaços/hífens com mesmas regras (sem remover acentos)
                    $key = strtolower($s);
                    return ['key' => $key, 'label' => $label];
                })
                ->filter(fn($it) => is_array($it) && ! empty($it['label']))
                ->unique('key')
                ->sortBy('key')
                ->values();
        }

        $dependencias = collect();
        if ($dependenciaCol) {
            $depsRaw = DB::table('escolas')
                ->select($dependenciaCol)
                ->whereNotNull($dependenciaCol)
                ->distinct()
                ->pluck($dependenciaCol);

            // Normaliza valores: colapsa espaços e normaliza hífens; gera label e key
            $dependencias = collect($depsRaw)
                ->map(function ($d) {
                    if (! is_string($d)) return null;
                    $s = trim($d);
                    $s = str_replace(["—","–","‑","−"], "-", $s);
                    $s = preg_replace('/\s*-\s*/u', '-', $s);
                    $s = preg_replace('/\s+/u', ' ', $s);

                    $label = mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
                    $key = strtolower($s);
                    return ['key' => $key, 'label' => $label];
                })
                ->filter(fn($it) => is_array($it) && ! empty($it['label']))
                ->unique('key')
                ->sortBy('key')
                ->values();
        }

        return view('mapas.escolas', compact('dres', 'municipios', 'dependencias')); // view Blade que vai renderizar o mapa
    }

    public function escolasGeoJson(Request $request)
    {
        $dreId = $request->input('dre_id');
        $municipioFiltro = $request->input('municipio');
        $jurisdicaoFiltro = $request->input('jurisdicao');
        // Detecta colunas disponíveis na tabela 'escolas' (case-insensitive)
        $cols = Schema::getColumnListing('escolas');
        Log::info('MapaController: colunas detectadas em escolas', ['cols' => $cols]);
        $lcMap = [];
        foreach ($cols as $c) {
            $lcMap[strtolower($c)] = $c;
        }

        $find = function (array $cands) use ($lcMap) {
            foreach ($cands as $cand) {
                $k = strtolower($cand);
                if (isset($lcMap[$k])) {
                    return $lcMap[$k];
                }
            }

            return null;
        };

        $idCol = $find(['id', 'id_escola']);
        $nomeCol = $find(['escola', 'nome']);
        $municipioCol = $find(['municipio']);
        $dependenciaCol = $find(['dependencia_administrativa', 'dependencia']);
        $codigoInepCol = $find(['codigo_inep', 'codigoinep', 'inep']);
        $latCol = $find(['latitude', 'lat']);
        $lonCol = $find(['longitude', 'lon']);
        $dreCol = $find(['dre']);

        // Se não há colunas de coordenadas, retorna vazio
        if (! $latCol || ! $lonCol) {
            return response()->json(['type' => 'FeatureCollection', 'features' => []]);
        }

        // Monta seleção com aliases consistentes
        $selects = [];
        // prefixar colunas provenientes da tabela 'escolas' para evitar ambiguidade em joins
        if ($idCol) {
            $selects[] = "escolas.$idCol as id";
        }
        if ($nomeCol) {
            $selects[] = "escolas.$nomeCol as nome";
        }
        if ($municipioCol) {
            $selects[] = "escolas.$municipioCol as municipio";
        }
        if ($dependenciaCol) {
            $selects[] = "escolas.$dependenciaCol as dependencia";
        }
        if ($codigoInepCol) {
            $selects[] = "escolas.$codigoInepCol as codigo_inep";
        }
        $selects[] = "escolas.$latCol as latitude";
        $selects[] = "escolas.$lonCol as longitude";

        $query = DB::table('escolas')->selectRaw(implode(', ', $selects))
            ->whereNotNull('escolas.' . $latCol)
            ->whereNotNull('escolas.' . $lonCol);

        // Aplica filtros opcionais
        if ($municipioFiltro && $municipioCol) {
            // Normaliza filtro recebido (mesmas regras da key gerada)
            $f = trim($municipioFiltro);
            $f = str_replace(["—","–","‑","−"], "-", $f);
            $f = preg_replace('/\s*-\s*/u', '-', $f);
            $f = preg_replace('/\s+/u', ' ', $f);
            $f = strtolower($f);

            // Expressão SQL que normaliza o valor do banco para comparação
            $expr = 'LOWER(' .
                'REPLACE(' .
                    'REPLACE(' .
                        'REPLACE(' .
                            'REPLACE(' .
                                'REPLACE(' .
                                    'REPLACE(TRIM(COALESCE(escolas.' . $municipioCol . ', "")), "—", "-"), "–", "-"), "‑", "-"), "−", "-"), " - ", "-"), "  ", " ")' .
            ')';

            // Duas passagens para reduzir possíveis espaços múltiplos
            $expr = 'LOWER(REPLACE(' . $expr . ', "  ", " "))';

            $query->whereRaw($expr . ' = ?', [$f]);
        }
        if ($jurisdicaoFiltro && $dependenciaCol) {
            // Normaliza filtro de jurisdição com mesmas regras de key
            $f = trim($jurisdicaoFiltro);
            $f = str_replace(["—","–","‑","−"], "-", $f);
            $f = preg_replace('/\s*-\s*/u', '-', $f);
            $f = preg_replace('/\s+/u', ' ', $f);
            $f = strtolower($f);

            $expr = 'LOWER(' .
                'REPLACE(' .
                    'REPLACE(' .
                        'REPLACE(' .
                            'REPLACE(' .
                                'REPLACE(' .
                                    'REPLACE(TRIM(COALESCE(escolas.' . $dependenciaCol . ', "")), "—", "-"), "–", "-"), "‑", "-"), "−", "-"), " - ", "-"), "  ", " ")' .
            ')';
            $expr = 'LOWER(REPLACE(' . $expr . ', "  ", " "))';

            $query->whereRaw($expr . ' = ?', [$f]);
        }

        // Se houver coluna 'dre' e tabela 'dres', tente fazer join para obter nome da dre
        $dreNomeAlias = null;
        if ($dreCol && Schema::hasTable('dres')) {
            // tenta associar escolas.dre = dres.id ou dres.codigodre
            $dresCols = Schema::getColumnListing('dres');
            $dresMap = array_map('strtolower', $dresCols);
            $joinOn = null;
            if (in_array('id', $dresMap)) {
                $joinOn = ['escolas.' . $dreCol, '=', 'dres.id'];
            } elseif (in_array('codigodre', $dresMap)) {
                $joinOn = ['escolas.' . $dreCol, '=', 'dres.codigodre'];
            }
            if ($joinOn) {
                $query->leftJoin('dres', $joinOn[0], $joinOn[1], $joinOn[2]);
                if (in_array('nome_dre', $dresMap)) {
                    $query->addSelect(DB::raw('dres.nome_dre as dre_nome'));
                    $dreNomeAlias = 'dre_nome';
                }

                // Filtro por DRE quando possível
                if ($dreId) {
                    if ($joinOn[2] === 'dres.id') {
                        $query->where('dres.id', $dreId);
                    } elseif ($joinOn[2] === 'dres.codigodre') {
                        $query->where('dres.codigodre', $dreId);
                    } else {
                        $query->where('escolas.' . $dreCol, $dreId);
                    }
                }
            }
        }
        // Caso não tenha tabela dres ou não foi possível fazer o join, aplica filtro direto se houver valor
        if ($dreId && $dreCol && ! Schema::hasTable('dres')) {
            $query->where('escolas.' . $dreCol, $dreId);
        }

        $escolas = $query->get();
        Log::info('MapaController: query escolas executada', [
            'selects' => $selects,
            'dependenciaCol' => $dependenciaCol,
            'dreCol' => $dreCol,
            'count' => $escolas->count(),
        ]);

        // Converte para GeoJSON
        $features = $escolas->map(function ($escola) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $escola->longitude, (float) $escola->latitude],
                ],
                'properties' => [
                    'id' => $escola->id,
                    'nome' => $escola->nome ?? 'Sem nome',
                    'municipio' => $escola->municipio,
                    'dre' => $escola->dre_nome ?? ($escola->dependencia ?? null),
                    'inep' => $escola->codigo_inep,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
