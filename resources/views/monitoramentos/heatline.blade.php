@extends('layouts.app')

@section('title','Heatline – Disponibilidade')

@section('css')
<style>
    .heatbox {
        width: 16px;
        height: 16px;
        margin: 1px;
        border-radius: 3px;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0">
            <h4 class="fw-semibold text-secondary">
                <i class="fas fa-fire-alt me-2 text-primary"></i>
                Heatline – Disponibilidade dos Hosts
            </h4>
        </div>

        <div class="card-body bg-white" id="heat-container">
            Carregando dados…
        </div>
    </div>

</div>
@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", () => {

    fetch(`{{ route('api.monitoramentos.heatline') }}`)
        .then(r => r.json())
        .then(hosts => {

            let html = '<table class="table table-borderless align-middle w-100">';
            html += '<tbody>';

            hosts.forEach(h => {
                html += `<tr><td class="text-nowrap fw-bold">${h.host}</td><td>`;
                h.values.forEach(v => {
                    let cor = v === 1 ? '#28a745' : '#dc3545'; // verde / vermelho
                    html += `<span class="heatbox" style="background:${cor}"></span>`;
                });
                html += `</td></tr>`;
            });

            html += '</tbody></table>';

            document.getElementById('heat-container').innerHTML = html;
        });
});
</script>
@endsection
