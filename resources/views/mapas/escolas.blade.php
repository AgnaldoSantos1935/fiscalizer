@extends('layouts.app')

@section('title', 'Mapa Interativo - Escolas do Pará')

@section('content')
<div class="container-fluid mt-3">

    <!-- 🧭 Cabeçalho -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-success text-white rounded-top-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-map-marked-alt me-2"></i>Mapa Interativo das Escolas do Pará
            </h5>
            <small class="text-white-50">Fonte: DB_Fiscalizer / SEDUC-PA</small>
        </div>

        <div class="card-body bg-light p-3">
            <div class="row g-3">

                <!-- 🎯 Filtro por DRE -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Diretoria Regional (DRE):</label>
                    <select id="filtroDRE" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="Belém">Belém</option>
                        <option value="Ananindeua">Ananindeua</option>
                        <option value="Castanhal">Castanhal</option>
                        <option value="Marabá">Marabá</option>
                        <option value="Santarém">Santarém</option>
                        <option value="Altamira">Altamira</option>
                        <option value="Redenção">Redenção</option>
                        <option value="Itaituba">Itaituba</option>
                        <!-- ⚠️ Adicione as demais DREs -->
                    </select>
                </div>

                <!-- 🏙️ Filtro por Município -->
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-secondary">Município:</label>
                    <input type="text" id="filtroMunicipio" class="form-control form-control-sm"
                           placeholder="Digite parte do nome do município...">
                </div>

                <!-- 🔍 Botão de Aplicar -->
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-sm btn-primary w-100" onclick="filtrarMapa()">
                        <i class="fas fa-filter me-2"></i>Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🌍 Mapa Interativo -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <iframe id="iframeMapa"
                    src="{{ asset('mapas/mapa_painel_escolas_para.html') }}"
                    width="100%" height="720px"
                    style="border:none; border-radius:0 0 1rem 1rem;"></iframe>
        </div>
    </div>

</div>
@endsection

@section('js')
<script>
/**
 * 🔎 Filtro simulado de DRE e Município.
 * (Versão simples — o mapa será recarregado conforme filtro)
 *
 * Em versões futuras, pode-se integrar filtros dinâmicos via API.
 */
function filtrarMapa() {
    const dre = document.getElementById('filtroDRE').value.trim();
    const municipio = document.getElementById('filtroMunicipio').value.trim();
    const iframe = document.getElementById('iframeMapa');

    let baseUrl = "{{ asset('mapas/mapa_painel_escolas_para.html') }}";
    let params = [];

    if (dre) params.push(`dre=${encodeURIComponent(dre)}`);
    if (municipio) params.push(`municipio=${encodeURIComponent(municipio)}`);

    if (params.length > 0) {
        iframe.src = baseUrl + '?' + params.join('&');
    } else {
        iframe.src = baseUrl; // limpa filtros
    }
}
</script>
@endsection
