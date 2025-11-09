@extends('layouts.app')

@section('title', 'Histórico de Testes de Conectividade')

@section('content_header')
<h1><i class="fas fa-clipboard-list me-2 text-primary"></i> Testes de Conectividade</h1>
@stop

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <table id="tabelaTestes" class="table table-bordered table-striped table-hover w-100">
            <thead class="table-light">
                <tr>

                    <th>Host</th>
                    <th>IP Destino</th>
                    <th>Status</th>
                    <th>Latência</th>
                    <th>Perda</th>
                    <th>Modo</th>
                    <th>Executado Por</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection
@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function() {
    $('#tabelaTestes').DataTable({
        processing: false,
        serverSide: true,
        ajax: "{{ route('host_testes.index') }}",
        columns: [
            { data: 'id', name: 'id', visible: false},
            { data: 'host', name: 'host' },
            { data: 'ip_destino', name: 'ip_destino' },
            { data: 'status', name: 'status' },
            { data: 'latencia_ms', name: 'latencia_ms', render: d => d ? d + ' ms' : '—' },
            { data: 'perda_pacotes', name: 'perda_pacotes', render: d => d ? d + '%' : '—' },
            { data: 'modo', name: 'modo' },
            { data: 'executado_por', name: 'executado_por' },
            { data: 'created_at', name: 'created_at' },
            { data: 'acoes', name: 'acoes', orderable: false, searchable: false },
        ],
        language: { url: 'js/pt-BR.json' },
        order: [[0, 'desc']]
    });
});
</script>
@stop
