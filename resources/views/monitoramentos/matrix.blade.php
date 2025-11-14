@extends('layouts.app')
@section('title','Cross-DRE Latency Matrix')

@section('css')
<style>
    .matrix-cell {
        width: 60px;
        height: 40px;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
        border-radius: 6px;
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white">
            <h4 class="text-secondary fw-semibold">
                <i class="fas fa-project-diagram me-2 text-primary"></i>
                Cross-DRE – Matriz de Latência
            </h4>
        </div>

        <div class="card-body" id="matrix-container">
            Carregando matriz…
        </div>
    </div>

</div>
@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", () => {

    fetch(`{{ route('api.monitoramentos.matrix') }}`)
        .then(r => r.json())
        .then(res => {
            const dres = res.dres;
            const matrix = res.matrix;

            let html = '<table class="table table-bordered text-center">';
            html += '<thead><tr><th></th>';

            dres.forEach(d => html += `<th>${d}</th>`);
            html += '</tr></thead><tbody>';

            matrix.forEach((linha, i) => {
                html += `<tr><th>${dres[i]}</th>`;
                linha.forEach(c => {

                    let cor = '#6c757d'; // cinza default

                    if (c !== null) {
                        if (c < 20) cor = '#198754';      // verde
                        else if (c < 40) cor = '#ffc107'; // amarelo
                        else cor = '#dc3545';             // vermelho
                    }

                    html += `<td><div class="matrix-cell" style="background:${cor}">
                        ${c !== null ? (c+' ms') : '—'}
                    </div></td>`;
                });
                html += '</tr>';
            });

            html += '</tbody></table>';

            document.getElementById('matrix-container').innerHTML = html;
        });

});
</script>
@endsection
