@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="card">
    <div class="card-header bg-primary text-white">Monitoramento de IPs e Links</div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Alvo (IP / Link)</th>
                    <th>Status</th>
                    <th>Latência (ms)</th>
                    <th>Status HTTP</th>
                    <th>Última Verificação</th>
                    <th>Erro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itens as $item)
                <tr>
                    <td>{{ $item->nome }}</td>
                    <td>{{ strtoupper($item->tipo) }}</td>
                    <td>{{ $item->alvo }}</td>
                    <td>
                        @if($item->online)
                            <span class="badge bg-success">ONLINE</span>
                        @else
                            <span class="badge bg-danger">OFFLINE</span>
                        @endif
                    </td>
                    <td>{{ $item->latencia ?? '-' }}</td>
                    <td>{{ $item->status_code ?? '-' }}</td>
                    <td>{{ $item->ultima_verificacao ? $item->ultima_verificacao->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $item->erro ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
