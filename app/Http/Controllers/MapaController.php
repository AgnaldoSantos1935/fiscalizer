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
        // Lista de DREs disponíveis para filtro
        $dres = DB::table('dres')
            ->select('id', 'nome_dre')
            ->orderBy('nome_dre')
            ->get();

        return view('mapas.escolas', compact('dres')); // view Blade que vai renderizar o mapa
    }

    public function escolasGeoJson(Request $request)
    {
        $dreId = $request->input('dre_id');
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
            ->whereNotNull('escolas.'.$latCol)
            ->whereNotNull('escolas.'.$lonCol);

        // Se houver coluna de dependencia administrativa, filtre por 'estadual'
        if ($dependenciaCol) {
            $query->whereRaw('LOWER(COALESCE('.$dependenciaCol.', "")) = ?', [strtolower('estadual')]);
        }

        // Se houver coluna 'dre' e tabela 'dres', tente fazer join para obter nome da dre
        $dreNomeAlias = null;
        if ($dreCol && Schema::hasTable('dres')) {
            // tenta associar escolas.dre = dres.id ou dres.codigodre
            $dresCols = Schema::getColumnListing('dres');
            $dresMap = array_map('strtolower', $dresCols);
            $joinOn = null;
            if (in_array('id', $dresMap)) {
                $joinOn = ['escolas.'.$dreCol, '=', 'dres.id'];
            } elseif (in_array('codigodre', $dresMap)) {
                $joinOn = ['escolas.'.$dreCol, '=', 'dres.codigodre'];
            }
            if ($joinOn) {
                $query->leftJoin('dres', $joinOn[0], $joinOn[1], $joinOn[2]);
                if (in_array('nome_dre', $dresMap)) {
                    $query->addSelect(DB::raw('dres.nome_dre as dre_nome'));
                    $dreNomeAlias = 'dre_nome';
                }
            }
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
