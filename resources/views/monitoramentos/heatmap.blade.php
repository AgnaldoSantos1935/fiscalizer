@extends('layouts.app')

@section('title','Mapa de Disponibilidade por Escola')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
@endsection

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0">
            <h4 class="card-title mb-0 fw-semibold">
                <i class="fas fa-map-marked-alt me-2 text-primary"></i> Heatmap de Disponibilidade
            </h4>
        </div>
        <div class="card-body p-0">
            <div id="map" class="ui-map h-80vh"></div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('map').setView([-3.5, -52], 5); // Pará

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(map);

    fetch(`{{ route('api.monitoramentos.heatmap') }}`)
        .then(r => r.json())
        .then(pontos => {
            pontos.forEach(p => {
                const cor = p.online ? 'green' : 'red';

                const marker = L.circleMarker([p.lat, p.lng], {
                    radius: 8,
                    color: cor,
                    fillColor: cor,
                    fillOpacity: 0.7
                }).addTo(map);

                marker.bindPopup(`
                    <strong>${p.escola ?? 'Escola N/D'}</strong><br>
                    DRE: ${p.dre ?? 'N/D'}<br>
                    Host: ${p.nome}<br>
                    Status: ${p.online ? 'ONLINE' : 'OFFLINE'}<br>
                    Latência: ${p.latencia ?? '—'} ms
                `);
            });
        });
});
</script>
@endsection
