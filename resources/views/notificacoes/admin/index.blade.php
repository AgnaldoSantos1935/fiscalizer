@extends('layouts.app')

@section('subtitle', 'Admin - Notificações')
@section('content_header_title', 'Administração de Notificações')
@section('content_header_subtitle', 'Eventos e templates')

@section('content_body')
<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0">
            <i class="fas fa-bell me-2"></i>
            Eventos de Notificação
        </h3>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.notificacoes.import') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-import me-1"></i> Importar do config
                </button>
            </form>
            <form action="{{ route('admin.notificacoes.sync') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-sync-alt me-1"></i> Sincronizar Actions
                </button>
            </form>
            
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">Código</th>
                        <th>Título</th>
                        <th class="d-none d-md-table-cell">Mensagem</th>
                        <th>Domínio</th>
                        <th class="text-nowrap">Prioridade</th>
                        <th class="text-nowrap">Canais</th>
                        <th class="text-center" style="width: 110px">Status</th>
                        <th class="text-nowrap" style="width: 160px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $e)
                        <tr>
                            <td class="text-monospace">{{ $e->codigo }}</td>
                            <td>{{ $e->title }}</td>
                            <td class="d-none d-md-table-cell text-muted">{{ \Illuminate\Support\Str::limit($e->message, 80) }}</td>
                            <td>{{ $e->dominio ?? '—' }}</td>
                            <td>
                                @php($prio = $e->priority ?? 'normal')
                                <span class="badge {{ $prio==='critical' ? 'bg-danger' : ($prio==='high' ? 'bg-warning text-dark' : ($prio==='low' ? 'bg-secondary' : 'bg-info')) }}">{{ ucfirst($prio) }}</span>
                            </td>
                            <td>
                                @php($chs = $e->channels ?? ['database'])
                                <span class="badge bg-secondary">{{ implode(', ', $chs) }}</span>
                            </td>
                            <td class="text-center">
                                @if($e->enabled)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.notificacoes.edit', $e) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.notificacoes.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('Remover evento?');">
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
                            <td colspan="7" class="text-center text-muted py-4">
                                Nenhum evento cadastrado. Importe do config ou cadastre um evento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="small text-muted">
            Total: {{ $events->total() }}
        </div>
        <div>
            {{ $events->links() }}
        </div>
    </div>
</div>
@endsection
