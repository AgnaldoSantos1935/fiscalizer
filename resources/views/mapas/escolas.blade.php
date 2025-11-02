@extends('layouts.app')

@section('title', 'Mapa de Escolas do Pará')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Mapa de Escolas do Pará</h4>

            <div class="d-flex align-items-center">
                <label for="filtroDre" class="me-2 mb-0">Filtrar por DRE:</label>
                <select id="filtroDre" class="form-select form-select-sm w-auto">
                    <option value="">Todas</option>
                    @foreach($dres as $dre)
                        <option value="{{ $dre->id }}">{{ $dre->nome_dre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-body p-0 position-relative">
            <div id="map" style="height: 80vh; border-radius: 0 0 1rem 1rem;"></div>
            <div id="mapInfo" class="map-info" style="position:absolute; top:10px; right:10px; z-index:1000; background:rgba(255,255,255,0.9); padding:8px 12px; border-radius:6px; font-size:0.9rem;">Carregando...</div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<style>
#map { width: 100%; height: 80vh; }
.leaflet-popup-content { font-size: 0.9rem; }
.map-info { box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cria o mapa centralizado no Pará
    const map = L.map('map').setView([-3.5, -52.0], 5.8);

    // Camada base
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let layerEscolas;
    const infoDiv = document.getElementById('mapInfo');

    // Função para carregar escolas via GeoJSON
    function carregarEscolas(dreId = '') {
        infoDiv.textContent = 'Carregando...';
        const url = '{{ route("api.escolas") }}' + (dreId ? '?dre_id=' + dreId : '');

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(async r => {
                // Se a resposta não for OK, loga o corpo (provavelmente HTML de login/erro)
                if (!r.ok) {
                    const txt = await r.text();
                    console.error('Erro ao carregar escolas: HTTP', r.status, txt);
                    infoDiv.textContent = 'Erro ao carregar dados (HTTP ' + r.status + '). Veja console.';
                    // interrompe pipeline retornando FeatureCollection vazio
                    return { type: 'FeatureCollection', features: [] };
                }

                const ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') === -1) {
                    // servidor retornou HTML (ex.: página de login) — logue e avise
                    const txt = await r.text();
                    console.error('Resposta inesperada do servidor (não JSON):', txt);
                    infoDiv.textContent = 'Resposta inesperada do servidor. Verifique console.';
                    return { type: 'FeatureCollection', features: [] };
                }

                return r.json();
            })
            .then(data => {
                if (layerEscolas) {
                    map.removeLayer(layerEscolas);
                }

                // Se o endpoint já retorna GeoJSON FeatureCollection, use direto.
                let geojson;
                if (data && data.type === 'FeatureCollection') {
                    geojson = data;
                } else if (Array.isArray(data)) {
                    // Converte array de linhas para GeoJSON. Suporta várias variantes de nomes de campos
                    const detectNumber = v => {
                        if (v === null || v === undefined) return NaN;
                        if (typeof v === 'number') return v;
                        // troca vírgula por ponto e remove espaços
                        const s = String(v).replace(',', '.').trim();
                        const n = parseFloat(s);
                        return Number.isFinite(n) ? n : NaN;
                    };

                    const latKeys = ['latitude','Latitude','lat','Lat','LATITUDE','LAT'];
                    const lonKeys = ['longitude','Longitude','lon','Lon','LONGITUDE','LON'];

                    const features = data.map(row => {
                        // procura propriedades de latitude/longitude em várias formas
                        let lat, lon;
                        for (const k of latKeys) { if (row[k] !== undefined) { lat = detectNumber(row[k]); break; } }
                        for (const k of lonKeys) { if (row[k] !== undefined) { lon = detectNumber(row[k]); break; } }

                        // fallback: nomes abreviados 'lat'/'lon' dentro de objeto properties
                        if ((lat === undefined || Number.isNaN(lat)) && row.lat !== undefined) lat = detectNumber(row.lat);
                        if ((lon === undefined || Number.isNaN(lon)) && row.lon !== undefined) lon = detectNumber(row.lon);

                        // se ainda inválido, tenta campos invertidos (alguns dumps usam lat/lon invertidos)
                        if ((Number.isNaN(lat) || Number.isNaN(lon)) && row.latitude && row.longitude) {
                            lat = detectNumber(row.latitude);
                            lon = detectNumber(row.longitude);
                        }

                        return {
                            type: 'Feature',
                            geometry: {
                                type: 'Point',
                                coordinates: [lon, lat]
                            },
                            properties: Object.assign({}, row)
                        };
                    })
                    // filtra features com coordenadas válidas
                    .filter(f => Array.isArray(f.geometry.coordinates) && Number.isFinite(f.geometry.coordinates[0]) && Number.isFinite(f.geometry.coordinates[1]));

                    geojson = { type: 'FeatureCollection', features };
                } else {
                    geojson = { type: 'FeatureCollection', features: [] };
                }

                if (!geojson.features || geojson.features.length === 0) {
                    infoDiv.textContent = 'Nenhuma escola encontrada para o filtro selecionado.';
                    return;
                }

                infoDiv.textContent = geojson.features.length + ' escolas carregadas.';

                layerEscolas = L.geoJSON(geojson, {
                    pointToLayer: (feature, latlng) =>
                        L.circleMarker(latlng, {
                            radius: 5,
                            fillColor: '#007bff',
                            color: '#fff',
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.9
                        }).bindPopup(`
                            <strong>${feature.properties.nome}</strong><br>
                            Município: ${feature.properties.municipio}<br>
                            DRE: ${feature.properties.dre}<br>
                            INEP: ${feature.properties.inep ?? '-'}
                        `)
                }).addTo(map);

                // Ajusta o mapa para os marcadores
                try {
                    const bounds = layerEscolas.getBounds();
                    if (bounds.isValid()) map.fitBounds(bounds.pad(0.1));
                } catch (e) {
                    // ignore
                }
            })
            .catch(err => {
                console.error('Erro ao carregar escolas:', err);
                infoDiv.textContent = 'Erro ao carregar dados.';
            });
    }

    // Carrega todas ao iniciar
    carregarEscolas();

    // Filtro de DRE
    document.getElementById('filtroDre').addEventListener('change', function () {
        carregarEscolas(this.value);
    });
});
</script>
@endsection
