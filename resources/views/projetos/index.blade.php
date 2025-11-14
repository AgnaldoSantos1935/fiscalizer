@extends('layouts.app')
@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Projetos de Software')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-secondary">
                <i class="fas fa-code-branch me-2 text-primary"></i>Projetos de Software
            </h4>
            <a href="{{ route('projetos.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Novo
            </a>
        </div>

        <div class="card-body">

            <form method="get" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="form-control" placeholder="Buscar por código, título, sistema...">
                </div>

                <div class="col-md-3">
                    <select name="situacao" class="form-select">
                        <option value="">Todas as Situações</option>
                        @foreach($situacoes as $key => $label)
                            <option value="{{ $key }}" @selected(request('situacao') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Sistema/Módulo</th>
                            <th>PF Planejado</th>
                            <th>Situação</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projetos as $p)
                            <tr>
                                <td class="fw-semibold">{{ $p->codigo }}</td>
                                <td>{{ $p->titulo }}</td>
                                <td>{{ $p->sistema }} @if($p->modulo) / {{ $p->modulo }} @endif</td>
                                <td>{{ number_format($p->pf_planejado ?? 0, 2, ',', '.') }}</td>
                                <td>
                                    @php
                                        $mapBadge = [
                                            'analise'              => 'secondary',
                                            'planejado'            => 'info',
                                            'em_execucao'          => 'primary',
                                            'homologacao'          => 'warning',
                                            'aguardando_pagamento' => 'dark',
                                            'concluido'            => 'success',
                                            'suspenso'             => 'danger',
                                            'cancelado'            => 'danger',
                                        ];
                                        $clsBadge = $mapBadge[$p->situacao] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $clsBadge }}">
                                        {{ $situacoes[$p->situacao] ?? $p->situacao }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="{{ route('projetos.show', $p->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-outline-secondary"
                                       href="{{ route('projetos.edit', $p->id) }}">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Nenhum projeto encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $projetos->withQueryString()->links() }}

        </div>
    </div>

</div>
@endsection
