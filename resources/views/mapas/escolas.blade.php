@extends('layouts.app')

@section('title', 'Mapa de Escolas do Pará')

@section('content')
<br>
@include('layouts.components.breadcrumbs')
<div class="container">
    <div class="card shadow-sm border-0 rounded-2 ui-card">
        <div id="mapCardHeader" class="card-header ui-card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Mapa de Escolas do Pará</h4>

            <div class="row align-items-end">
                <div class="col-12 col-md-3 mb-2">
                    <label for="filtroDre" class="ui-form-label mb-1">DRE</label>
                    <select id="filtroDre" class="form-select form-select-sm w-100 ui-select">
                        <option value="">Todas</option>
                        @foreach($dres as $dre)
                            <option value="{{ is_array($dre) ? $dre['id'] : $dre->id }}">{{ is_array($dre) ? $dre['label'] : $dre->nome_dre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4 mb-2">
                    <label for="filtroMunicipio" class="ui-form-label mb-1">Município</label>
                    <select id="filtroMunicipio" class="form-select form-select-sm w-100 ui-select">
                        <option value="">Todos</option>
                        @isset($municipios)
                            @foreach($municipios as $m)
                                <option value="{{ is_array($m) ? $m['key'] : $m }}">{{ is_array($m) ? $m['label'] : $m }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div class="col-12 col-md-3 mb-2">
                    <label for="filtroJurisdicao" class="ui-form-label mb-1">Jurisdição</label>
                    <select id="filtroJurisdicao" class="form-select form-select-sm w-100 ui-select">
                        <option value="">Todas</option>
                        @isset($dependencias)
                            @foreach($dependencias as $dep)
                                <option value="{{ is_array($dep) ? $dep['key'] : $dep }}">{{ is_array($dep) ? $dep['label'] : ucfirst($dep) }}</option>
                            @endforeach
                        @else
                            <option value="estadual">Estadual</option>
                            <option value="municipal">Municipal</option>
                        @endisset
                    </select>
                </div>

                <div class="col-12 col-md-2 mb-2 d-flex align-items-end justify-content-md-end justify-content-start">
                    <button id="btnLimparFiltros" class="btn btn-sm ui-btn">
                        <i class="fas fa-eraser me-1"></i>Limpar filtros
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0 position-relative">
            <div id="map" class="ui-map rounded-bottom-1rem"></div>
            <div id="mapInfo" class="map-info ui-map-info position-absolute" style="top:10px; right:10px;">
             Carregando...
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<style>
/* Regras globais cobrem o container (#map.ui-map); manter apenas específicas locais */
.leaflet-popup-content { font-size: 0.9rem; }
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cria o mapa centralizado no Pará
    const map = L.map('map').setView([-3.5, -52.0], 5.8);
    // Ajuste de tamanho do Leaflet ao redimensionar
    window.addEventListener('resize', () => { map.invalidateSize(); });

    // Camada base
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let layerEscolas;
    const infoDiv = document.getElementById('mapInfo');

    // Função para carregar escolas via GeoJSON
    function carregarEscolas() {
        infoDiv.textContent = 'Carregando...';
        const dreId = document.getElementById('filtroDre').value || '';
        const municipio = document.getElementById('filtroMunicipio').value || '';
        const jurisdicao = document.getElementById('filtroJurisdicao').value || '';

        const params = new URLSearchParams();
        if (dreId) params.set('dre_id', dreId);
        if (municipio) params.set('municipio', municipio);
        if (jurisdicao) params.set('jurisdicao', jurisdicao);

        const url = '{{ route("api.escolas") }}' + (params.toString() ? ('?' + params.toString()) : '');

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
                            fillColor: '#006ce3',
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

                // garante ajuste de tamanho após renderização dos pontos
                map.invalidateSize();

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

    // Aplica filtros iniciais da URL (persistência)
    const qs = new URLSearchParams(location.search);
    const iniDre = qs.get('dre_id');
    const iniMun = qs.get('municipio');
    const iniJur = qs.get('jurisdicao');
    if (iniDre !== null) document.getElementById('filtroDre').value = iniDre;
    if (iniMun !== null) document.getElementById('filtroMunicipio').value = iniMun;
    if (iniJur !== null) document.getElementById('filtroJurisdicao').value = iniJur;

    // Carrega ao iniciar
    carregarEscolas();

    // Filtro de DRE
    function atualizarURL() {
        const dreId = document.getElementById('filtroDre').value || '';
        const municipio = document.getElementById('filtroMunicipio').value || '';
        const jurisdicao = document.getElementById('filtroJurisdicao').value || '';
        const params = new URLSearchParams();
        if (dreId) params.set('dre_id', dreId);
        if (municipio) params.set('municipio', municipio);
        if (jurisdicao) params.set('jurisdicao', jurisdicao);
        const newUrl = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
        history.replaceState(null, '', newUrl);
    }

    function onFiltroChange() {
        atualizarURL();
        carregarEscolas();
    }

    // Desativa DRE e Jurisdição quando Município estiver selecionado
    function atualizarBloqueioPorMunicipio() {
        const selMun = document.getElementById('filtroMunicipio');
        const selDre = document.getElementById('filtroDre');
        const selJur = document.getElementById('filtroJurisdicao');
        const hasMun = (selMun.value || '').trim() !== '';

        if (hasMun) {
            // Limpa valores para evitar combinação de filtros
            selDre.value = '';
            selJur.value = '';
        }

        selDre.disabled = hasMun;
        selJur.disabled = hasMun;
    }

    // Ao usar DRE, reativa os demais e limpa Município
    function atualizarBloqueioPorDre() {
        const selMun = document.getElementById('filtroMunicipio');
        const selDre = document.getElementById('filtroDre');
        const selJur = document.getElementById('filtroJurisdicao');
        const hasDre = (selDre.value || '').trim() !== '';

        if (hasDre) {
            // Limpa Município e garante que todos estejam ativos
            selMun.value = '';
            selDre.disabled = false;
            selJur.disabled = false;
            selMun.disabled = false;
        }

        // Reavalia bloqueio por Município (caso vazio ou pré-selecionado)
        atualizarBloqueioPorMunicipio();
    }

    document.getElementById('filtroDre').addEventListener('change', () => {
        atualizarBloqueioPorDre();
        onFiltroChange();
    });
    document.getElementById('filtroMunicipio').addEventListener('change', () => {
        atualizarBloqueioPorMunicipio();
        onFiltroChange();
    });
    document.getElementById('filtroJurisdicao').addEventListener('change', onFiltroChange);

    // Botão limpar filtros: reseta selects, inputs e URL
    document.getElementById('btnLimparFiltros').addEventListener('click', (e) => {
        e.preventDefault();
        const selDre = document.getElementById('filtroDre');
        const selMun = document.getElementById('filtroMunicipio');
        const selJur = document.getElementById('filtroJurisdicao');
        selDre.value = '';
        selMun.value = '';
        selJur.value = '';
        // Reativa DRE e Jurisdição ao limpar
        selDre.disabled = false;
        selJur.disabled = false;
        history.replaceState(null, '', window.location.pathname);
        carregarEscolas();
    });

    // Aplica bloqueio inicial conforme URL (se município estiver pré-selecionado)
    atualizarBloqueioPorMunicipio();
});
</script>
@endsection
