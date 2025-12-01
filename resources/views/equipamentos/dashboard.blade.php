@extends('layouts.app')

@section('title', 'Dashboard de Equipamentos')

@section('content_body')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            <i class="fas fa-chart-bar me-2 text-primary"></i>Dashboard de Equipamentos
        </h3>

        <a href="{{ route('equipamentos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-desktop me-1"></i> Ver lista de equipamentos
        </a>
    </div>

    <div class="row g-3 mb-3">
        <!-- Total -->
        <div class="col-md-3">
            <div class="card ui-card shadow-sm border-0 rounded-4">
                <div class="card-body bg-white text-center">
                    <div class="small text-muted mb-1">Total de equipamentos</div>
                    <div class="display-6 fw-semibold">{{ $total }}</div>
                </div>
            </div>
        </div>

        <!-- Ativos -->
        <div class="col-md-3">
            <div class="card ui-card shadow-sm border-0 rounded-4">
                <div class="card-body bg-white text-center">
                    <div class="small text-muted mb-1">Ativos (check-in ≤ 30 dias)</div>
                    <div class="display-6 fw-semibold text-success">{{ $ativos }}</div>
                </div>
            </div>
        </div>

        <!-- Obsoletos -->
        <div class="col-md-3">
            <div class="card ui-card shadow-sm border-0 rounded-4">
                <div class="card-body bg-white text-center">
                    <div class="small text-muted mb-1">Potencialmente obsoletos</div>
                    <div class="display-6 fw-semibold text-danger">{{ $obsoletos }}</div>
                </div>
            </div>
        </div>

        <!-- Sem check-in -->
        <div class="col-md-3">
            <div class="card ui-card shadow-sm border-0 rounded-4">
                <div class="card-body bg-white text-center">
                    <div class="small text-muted mb-1">Sem check-in recente / nunca</div>
                    <div class="display-6 fw-semibold text-warning">{{ $semCheckin }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <!-- Distribuição por Tipo -->
        <div class="col-md-6">
            <div class="card ui-card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header ui-card-header border-0">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-layer-group me-2 text-primary"></i>Distribuição por Tipo
                    </h5>
                </div>
                <div class="card-body bg-white small">
                    @if($porTipo->isEmpty())
                        <p class="text-muted mb-0">Nenhum equipamento cadastrado.</p>
                    @else
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th class="text-end">Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($porTipo as $linha)
                                    <tr>
                                        <td>{{ $linha->tipo ?? '—' }}</td>
                                        <td class="text-end">{{ $linha->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <!-- Distribuição por Origem -->
        <div class="col-md-6">
            <div class="card ui-card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header ui-card-header border-0">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-database me-2 text-primary"></i>Distribuição por Origem do Inventário
                    </h5>
                </div>
                <div class="card-body bg-white small">
                    @if($porOrigem->isEmpty())
                        <p class="text-muted mb-0">Nenhum equipamento cadastrado.</p>
                    @else
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Origem</th>
                                    <th class="text-end">Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($porOrigem as $linha)
                                    <tr>
                                        <td>{{ $linha->origem_inventario ?? '—' }}</td>
                                        <td class="text-end">{{ $linha->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top 10 unidades com mais equipamentos -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card ui-card shadow-sm border-0 rounded-4">
                <div class="card-header ui-card-header border-0">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-school me-2 text-primary"></i>Top 10 unidades com mais equipamentos
                    </h5>
                </div>
                <div class="card-body bg-white small">
                    @if($porUnidade->isEmpty())
                        <p class="text-muted mb-0">Nenhum equipamento cadastrado.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Unidade</th>
                                        <th class="text-end">Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($porUnidade as $linha)
                                        <tr>
                                            <td>{{ optional($linha->unidade)->nome ?? '—' }}</td>
                                            <td class="text-end">{{ $linha->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
