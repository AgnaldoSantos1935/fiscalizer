@extends('adminlte::page')

@section('title', 'Relatórios')

@section('content_header')
    <h1><i class="fas fa-chart-pie"></i> Relatórios</h1>
@stop

@section('content')
@include('layouts.components.breadcrumbs')
<div class="card">
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <input type="text" name="busca" class="form-control" placeholder="Buscar título..." value="{{ request('busca') }}">
            </div>
            <div class="col-md-3">
                <select name="tipo" class="form-control">
                    <option value="">-- Tipo --</option>
                    <option value="contrato" {{ request('tipo')=='contrato'?'selected':'' }}>Contrato</option>
                    <option value="medicao" {{ request('tipo')=='medicao'?'selected':'' }}>Medição</option>
                    <option value="empresa" {{ request('tipo')=='empresa'?'selected':'' }}>Empresa</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </form>

        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Criado por</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($relatorios as $relatorio)
                <tr>
                    <td>{{ $relatorio->id }}</td>
                    <td>{{ $relatorio->titulo }}</td>
                    <td>{{ ucfirst($relatorio->tipo) }}</td>
                    <td>{{ $relatorio->user->name ?? '-' }}</td>
                    <td>{{ $relatorio->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>{{ $relatorios->links() }}</div>

            <div>
                <a href="{{ route('relatorios.export.excel') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </a>
                <a href="{{ route('relatorios.export.pdf') }}" target="_blank" rel="noopener" class="btn btn-danger btn-sm">
<i class="fas fa-file-pdf"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@stop
