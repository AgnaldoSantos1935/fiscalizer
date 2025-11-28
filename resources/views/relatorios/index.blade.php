@extends('adminlte::page')

@section('title', 'Relatórios')

@section('content_header')
    <h1 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Relatórios</h1>
@stop

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 text-secondary fw-semibold"><i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa</h5>
        </div>
        <div class="card-body bg-white">
            <form method="GET" class="row g-3 bg-light p-3 rounded-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Buscar título</label>
                    <input type="text" name="busca" class="form-control form-control-sm" placeholder="Digite parte do título" value="{{ request('busca') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Tipo</label>
                    <select name="tipo" class="form-control form-control-sm">
                        <option value="">--</option>
                        <option value="contrato" {{ request('tipo')=='contrato'?'selected':'' }}>Contrato</option>
                        <option value="medicao" {{ request('tipo')=='medicao'?'selected':'' }}>Medição</option>
                        <option value="empresa" {{ request('tipo')=='empresa'?'selected':'' }}>Empresa</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control form-control-sm" value="{{ request('data_inicio') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control form-control-sm" value="{{ request('data_fim') }}">
                </div>

                <div class="col-12 text-end mt-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-filter me-1"></i> Aplicar filtros
                    </button>
                    <a href="{{ route('relatorios.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="fas fa-undo me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold"><i class="fas fa-list-alt me-2 text-primary"></i>Lista de Relatórios</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('relatorios.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Exportar Excel
                </a>
                <a href="{{ route('relatorios.export.pdf', request()->query()) }}" target="_blank" rel="noopener" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
                <a href="{{ route('relatorios.gerar') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Gerar Relatório
                </a>
            </div>
        </div>

        <div class="card-body bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">ID</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Criado por</th>
                            <th class="text-nowrap">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($relatorios as $relatorio)
                        <tr>
                            <td>{{ $relatorio->id }}</td>
                            <td>{{ $relatorio->titulo }}</td>
                            <td><span class="badge bg-info text-dark text-uppercase">{{ $relatorio->tipo }}</span></td>
                            <td>{{ $relatorio->user->name ?? '—' }}</td>
                            <td>{{ optional($relatorio->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>{{ $relatorios->links() }}</div>
                <small class="text-muted">Total: {{ $relatorios->total() }}</small>
            </div>
        </div>
    </div>

</div>
@stop
