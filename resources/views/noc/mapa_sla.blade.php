@extends('layouts.app')
@section('title','Mapa SLA – Contratos')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0">
            <h4 class="text-secondary fw-semibold">
                <i class="fas fa-map-marked-alt me-2 text-primary"></i>
                Mapa de SLA dos Contratos
            </h4>
        </div>

        <div class="card-body p-0">
            <div id="mapSLA" style="height: 80vh;"></div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const map = L.map('mapSLA').setView([-3.5, -52], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(map);

    fetch("{{ route('api.noc.mapa-sla') }}")
        .then(r => r.json())
        .then(data => {

            data.forEach(p => {

                if (!p.lat || !p.lng) return;

                let cor = "#6c757d"; // cinza

                if (p.sla_real !== null) {
                    if (p.sla_real >= p.sla_min)      cor = "#28a745"; // verde
                    else if (p.sla_real >= 95)        cor = "#ffc107"; // amarelo
                    else                               cor = "#dc3545"; // vermelho
                }

                const marker = L.circleMarker([p.lat, p.lng], {
                    radius: 10,
                    color: cor,
                    fillColor: cor,
                    fillOpacity: 0.8
                }).addTo(map);

                marker.bindPopup(`
                    <strong>${p.escola ?? p.nome}</strong><br>
                    SLA Real: ${p.sla_real ?? 'N/A'}%<br>
                    SLA Mínimo: ${p.sla_min}%<br>
                `);
            });

        });
});
</script>
@endsection
