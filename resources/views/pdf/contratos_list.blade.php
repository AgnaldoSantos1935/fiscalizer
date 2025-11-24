@extends('pdf.layouts.base')

@section('title', 'Contratos')
@section('header_left', 'Relatório de Contratos — Fiscalizer')
@section('header_right', 'Lista resumida (máx. 200 registros)')

@section('content')
    <h1>Lista de Contratos</h1>
    <table>
        <thead>
            <tr>
                <th class="nowrap">ID</th>
                <th>Nº Contrato</th>
                <th>Contratada</th>
                <th class="nowrap">Início</th>
                <th class="nowrap">Fim</th>
                <th class="nowrap">Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contratos as $c)
                <tr>
                    <td class="nowrap">{{ $c->id }}</td>
                    <td>{{ $c->num_contrato }}</td>
                    <td>{{ $c->contratada }}</td>
                    <td class="nowrap">{{ optional($c->data_inicio)->format('d/m/Y') }}</td>
                    <td class="nowrap">{{ optional($c->data_fim)->format('d/m/Y') }}</td>
                    <td class="nowrap">{{ number_format((float)($c->valor_total ?? 0), 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection