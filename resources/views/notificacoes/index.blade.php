@extends('layouts.app')

@section('subtitle', 'Notificações')
@section('content_header_title', 'Notificações')
@section('content_header_subtitle', 'Todas as notificações')

@section('content_body')
<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0">
            <i class="fas fa-bell me-2"></i>
            Minhas notificações
        </h3>
        <form action="{{ route('notificacoes.todas') }}" method="POST" class="ms-auto">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-check-double me-1"></i> Marcar todas como lidas
            </button>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px">Status</th>
                        <th>Título</th>
                        <th class="d-none d-md-table-cell">Mensagem</th>
                        <th>Link</th>
                        <th class="text-nowrap" style="width: 160px">Recebida</th>
                        <th class="text-nowrap" style="width: 140px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($notificacoes as $n)
                    <tr>
                        <td class="text-center">
                            @if(!$n->lida)
                                <span class="badge bg-warning text-dark">Nova</span>
                            @else
                                <span class="badge bg-secondary">Lida</span>
                            @endif
                        </td>
                        <td>
                            {{ $n->titulo }}
                        </td>
                        <td class="d-none d-md-table-cell text-muted">
                            {{ $n->mensagem }}
                        </td>
                        <td>
                            @if($n->link)
                                <a href="{{ $n->link }}" class="text-primary" target="_blank">
                                    Abrir
                                    <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            {{ $n->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            @if(!$n->lida)
                                <form action="{{ route('notificacoes.lida', $n) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-check me-1"></i> Marcar lida
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="far fa-bell-slash me-1"></i> Sem notificações no momento
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="small text-muted">
            Total: {{ $notificacoes->total() }}
        </div>
        <div>
            {{ $notificacoes->links() }}
        </div>
    </div>
</div>
@endsection