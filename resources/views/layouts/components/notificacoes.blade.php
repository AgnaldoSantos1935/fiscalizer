
<li class="nav-item dropdown notif-dropdown {{ $notiCount > 0 ? 'notif-pulse' : '' }}">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-label="Notificações">
        @if($notiCount > 0)
            <i class="fas fa-bell text-warning"></i>
            <span class="badge badge-warning navbar-badge">{{ $notiCount }}</span>
        @else
            <i class="far fa-bell text-white"></i>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        <span class="dropdown-header">{{ $notiCount }} Notificações</span>
        <div class="dropdown-divider"></div>

        @forelse($ultimas as $n)
            <a href="{{ $n->link ?? '#' }}" class="dropdown-item">
                <i class="fas fa-info-circle mr-2"></i> {{ $n->titulo }}
                <span class="float-right text-muted text-sm">{{ $n->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <span class="dropdown-item text-muted">Sem notificações</span>
        @endforelse

        <div class="dropdown-divider"></div>
        <a href="javascript:void(0)" onclick="subscribePush()" class="dropdown-item">
            <i class="fas fa-bell"></i> Ativar notificações push
        </a>
        <a href="{{ route('notificacoes.index') }}" class="dropdown-item dropdown-footer">
            Ver todas
        </a>
    </div>
</li>
