@extends('layouts.app')

@section('title','Dashboard de Monitoramentos')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-signal me-2 text-primary"></i> Monitoramentos Mikrotik
            </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Conexão</th>
                            <th>Alvo</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hosts as $h)
                            <tr>
                                <td>{{ $h->nome_conexao }}</td>
                                <td>{{ $h->host_alvo }}</td>
                                <td>{{ $h->tipo_monitoramento }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Nenhum host cadastrado para Mikrotik.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

