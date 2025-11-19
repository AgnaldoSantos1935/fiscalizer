@extends('layouts.app')

@section('title', 'Mapa de Escolas do Pará')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
@endsection

@section('content')
@include('layouts.components.breadcrumbs')
<iframe src="{{ asset('mapas/mapa_escolas_para_dre_cluster.html') }}"
        width="100%" height="700px" class="border-0"></iframe>

@endsection

@section('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    // Inicializa o mapa centralizado no Pará
    const map = L.map('map').setView([-3.5, -52.0], 5);

    // Camada base OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Função de carregamento das escolas (GeoJSON)
    function carregarEscolas() {
        fetch('{{ url("/api/escolas") }}')
            .then(response => {
                if (!response.ok) throw new Error('Erro HTTP ' + response.status);
                return response.json();
            })
            .then(geojson => {
                if (!geojson.features || geojson.features.length === 0) {
                    console.warn('Nenhum ponto de escola retornado.');
                    return;
                }

                const layer = L.geoJSON(geojson, {
                    pointToLayer: (feature, latlng) =>
                        L.circleMarker(latlng, {
                            radius: 6,
                            color: '#007bff',
                            fillColor: '#007bff',
                            fillOpacity: 0.8
                        }),
                    onEachFeature: (feature, layer) => {
                        const p = feature.properties;
                        layer.bindPopup(`
                            <strong>${p.nome}</strong><br>
                            ${p.municipio} - ${p.dre ?? ''}
                        `);
                    }
                }).addTo(map);

                map.fitBounds(layer.getBounds(), { padding: [20, 20] });

                // Redimensiona mapa após renderização
                setTimeout(() => map.invalidateSize(), 500);
            })
            .catch(err => {
                console.error('Erro ao carregar GeoJSON:', err);
                alert('Erro ao carregar dados das escolas. Veja o console.');
            });
    }

    carregarEscolas();

    // Garante redimensionamento ao abrir colapsos/abas AdminLTE
    document.addEventListener('shown.bs.tab', () => map.invalidateSize());
    document.addEventListener('shown.bs.collapse', () => map.invalidateSize());
});
</script>
@endsection
