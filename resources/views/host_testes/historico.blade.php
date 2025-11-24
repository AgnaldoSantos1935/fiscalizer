@extends('layouts.app')

@section('title', 'Histórico de Testes de Conectividade')

@section('content_header')
<h1><i class="fas fa-history me-2 text-primary"></i> Histórico de Testes</h1>
@stop

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <!-- 🔹 Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form id="formFiltros" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Período Inicial</label>
                    <input type="date" name="inicio" class="form-control" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Período Final</label>
                    <input type="date" name="fim" class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Provedor</label>
                    <select name="provedor" class="form-select">
                        <option value="">Todos</option>
                        @foreach($provedores as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Conexão</label>
                    <select name="host_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($hosts as $h)
                            <option value="{{ $h->id }}">{{ $h->nome_conexao }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 Tabela -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <table id="tabelaHistorico" class="table table-striped table-bordered w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Conexão</th>
                        <th>Provedor</th>
                        <th>Status</th>
                        <th>Latência</th>
                        <th>Perda</th>
                        <th>Modo</th>
                        <th>Executado Por</th>
                        <th>Data</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
@stop
@section('css')
@endsection
@section('js')
<script>
$(document).ready(function() {
    const tabela = $('#tabelaHistorico').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: '{{ route("host_testes.historico") }}',

        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nome_conexao', name: 'nome_conexao' },
            { data: 'provedor', name: 'provedor' },
            { data: 'status_conexao', name: 'status_conexao', orderable: false, searchable: false },
            { data: 'latencia_ms', name: 'latencia_ms' },
            { data: 'perda_pacotes', name: 'perda_pacotes' },
            { data: 'modo_execucao', name: 'modo_execucao' },
            { data: 'executado_por', name: 'executado_por' },
            { data: 'created_at', name: 'created_at' }
        ],
        order: [[8, 'desc']],

        language: { url: '{{ asset("js/pt-BR.json") }}' },
        dom: 't<"bottom"ip>',
        pageLength: 25,
        responsive: true
    });

    // Atualizar tabela ao mudar filtros
    $('#formFiltros :input').on('change', function() {
        tabela.ajax.reload();
    });
});
</script>
@stop
