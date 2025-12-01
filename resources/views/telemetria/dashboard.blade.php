@extends('layouts.app')

@section('title', 'Monitoramento dos Agentes')

@section('content_header')
    <h1>Monitoramento dos Agentes de Inventário</h1>
@stop

@section('content')
@include('layouts.components.breadcrumbs')

<div class="row">
    <div class="col-md-3">
        <x-adminlte-info-box title="{{ $ativos }}" text="Agentes Online (10min)" theme="success" icon="fas fa-server"/>
    </div>

    <div class="col-md-3">
        <x-adminlte-info-box title="{{ $internetOk }}" text="Internet OK" theme="primary" icon="fas fa-wifi"/>
    </div>

    <div class="col-md-3">
        <x-adminlte-info-box title="{{ $atrasados }}" text="Sem Telemetria" theme="warning" icon="fas fa-clock"/>
    </div>

    <div class="col-md-3">
        <x-adminlte-info-box title="{{ $comErro }}" text="Com Erros" theme="danger" icon="fas fa-exclamation-triangle"/>
    </div>
</div>

<x-adminlte-card title="Últimas leituras das unidades">
    <table class="table table-sm table-hover">
        <thead>
            <tr>
                <th>Unidade</th>
                <th>Versão</th>
                <th>CPU</th>
                <th>RAM</th>
                <th>Internet</th>
                <th>Latência</th>
                <th>Último Envio</th>
                <th>Erro</th>
            </tr>
        </thead>

        <tbody>
            @foreach($telemetrias as $t)
            <tr>
                <td>{{ $t->unidade->nome }}</td>
                <td>{{ $t->agent_version }}</td>
                <td>{{ $t->cpu_usage }}%</td>
                <td>{{ $t->ram_used }} GB</td>
                <td>{{ $t->internet_status }}</td>
                <td>{{ $t->latency_ms }} ms</td>
                <td>{{ $t->created_at->diffForHumans() }}</td>
                <td>{{ Str::limit($t->last_error, 40) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $telemetrias->links() }}
</x-adminlte-card>

@endsection
