@extends('layouts.app')

@section('title', "Mapa de Downtime – Medição {$medicao->id}")

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
@endsection

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">
        <i class="fas fa-map-marked-alt text-primary"></i>
        Mapa de Downtime – Medição {{ $medicao->competencia }}
    </h3>

    <div id="map" style="height: 75vh;"></div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const pontos = @json($pontos);

    const map = L.map('map').setView([-3.5, -52.0], 5); // ajuste para o Pará

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    pontos.forEach(p => {
        const cor =
            p.uptime >= 99 ? 'green' :
            p.uptime >= 95 ? 'orange' : 'red';

        const marker = L.circleMarker([p.lat, p.lng], {
            radius: 8,
            weight: 2,
        }).addTo(map);

        marker.bindPopup(`
            <strong>${p.nome}</strong><br>
            Uptime: ${p.uptime}%<br>
            Downtime: ${p.downtime} min<br>
            Quedas: ${p.qtd_quedas}
        `);
    });
</script>
@endsection
