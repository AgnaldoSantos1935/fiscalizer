<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class MapaController extends Controller
{
    public function index()
    {
        return view('mapa'); // view Blade que vai renderizar o mapa
    }

    public function escolasGeoJson()
    {
        $rows = DB::table('escolas')
            ->select('codigo', 'Escola', 'Municipio', 'dre', 'Latitude AS lon', 'Longitude AS lat')
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude')
            ->get();

        $features = [];
        foreach ($rows as $r) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $r->codigo,
                    'nome' => $r->Escola,
                    'municipio' => $r->Municipio,
                    'dre' => $r->dre,
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$r->lon, (float)$r->lat],
                ],
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
