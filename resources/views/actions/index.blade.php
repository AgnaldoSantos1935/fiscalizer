@extends('layouts.app')

@section('title', 'Actions - RBAC')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Gerenciar Actions</h3>
        <a href="{{ route('actions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Nova Action
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-outline card-secondary">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 220px">Código</th>
                        <th>Nome</th>
                        <th style="width: 200px">Módulo</th>
                        <th style="width: 160px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actions as $action)
                        <tr>
                            <td class="text-monospace">{{ $action->codigo }}</td>
                            <td>{{ $action->nome }}</td>
                            <td>{{ $action->modulo ?? '—' }}</td>
                            <td>
                                <a href="{{ route('actions.show', $action) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('actions.edit', $action) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('actions.destroy', $action) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir esta action?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Nenhuma action cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $actions->links() }}
        </div>
    </div>
</div>
@endsection