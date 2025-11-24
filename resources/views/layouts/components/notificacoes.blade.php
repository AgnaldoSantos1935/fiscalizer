
<li class="nav-item dropdown notif-dropdown {{ ($notiCount ?? 0) > 0 ? 'notif-pulse' : '' }}">
    <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-label="Notificações">
        @if(($notiCount ?? 0) > 0)
            <i class="fas fa-bell text-warning"></i>
            <span class="badge bg-warning navbar-badge" id="notifCountBadge">{{ $notiCount ?? 0 }}</span>
        @else
            <i class="far fa-bell text-white"></i>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">

        <div class="d-flex align-items-center justify-content-between px-3 py-2">
            <span class="dropdown-header p-0 m-0">{{ $notiCount ?? 0 }} Notificações</span>
            @if(($notiCount ?? 0) > 0)
            <button class="btn btn-sm btn-outline-primary" onclick="markAllNotifications()">
                <i class="fas fa-check-double me-1"></i> Marcar todas
            </button>
            @endif
        </div>
        <div class="dropdown-divider"></div>

        @forelse(($ultimas ?? []) as $n)
            <div class="dropdown-item d-flex align-items-start justify-content-between {{ !$n->lida ? 'dropdown-item-unread fw-bold' : '' }}">
                <a href="{{ $n->link ?? '#' }}" class="flex-grow-1 text-reset notif-item" data-id="{{ $n->id }}" data-link="{{ $n->link ?? '#' }}">
                    @if(!$n->lida)
                        <i class="fas fa-dot-circle text-info me-2" title="Nova"></i>
                    @else
                        <i class="fas fa-info-circle me-2"></i>
                    @endif
                    {{ $n->titulo }}
                    @if(!$n->lida)
                        <span class="badge bg-info ms-2">Nova</span>
                    @endif
                    <span class="float-end text-muted text-sm">{{ $n->created_at->diffForHumans() }}</span>
                </a>
                @if(!$n->lida)
                <button class="btn btn-sm btn-outline-success ms-2" data-id="{{ $n->id }}" onclick="markNotificationRead({{ $n->id }})" title="Marcar como lida">
                    <i class="fas fa-check"></i>
                </button>
                @endif
            </div>
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

@push('scripts')
<script>
function markNotificationRead(id) {
  fetch("{{ url('/notificacoes') }}/" + id + "/lida", {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': window.CSRFToken,
      'Accept': 'application/json'
    }
  }).then(() => {
    try {
      // Atualiza badge decrementando e remove pulso quando zerar
      const badge = document.getElementById('notifCountBadge');
      if (badge) {
        const n = parseInt(badge.textContent || '0', 10);
        if (!isNaN(n) && n > 0) {
          const next = n - 1;
          badge.textContent = String(next);
          if (next === 0) {
            const li = document.querySelector('li.notif-dropdown');
            if (li) li.classList.remove('notif-pulse');
          }
        }
      }
      // Atualiza item visualmente no dropdown
      const btn = document.querySelector('.btn.btn-sm.btn-outline-success.ms-2[data-id="' + id + '"]');
      if (btn) {
        const wrapper = btn.closest('.dropdown-item');
        if (wrapper) {
          wrapper.classList.remove('dropdown-item-unread');
          wrapper.classList.remove('fw-bold');
          const dotIcon = wrapper.querySelector('i.fa-dot-circle');
          if (dotIcon) dotIcon.remove();
          const novaBadgeWarning = wrapper.querySelector('.badge.badge-warning');
          if (novaBadgeWarning) novaBadgeWarning.remove();
          const novaBadgeInfo = wrapper.querySelector('.badge.badge-info');
          if (novaBadgeInfo) novaBadgeInfo.remove();
        }
        btn.remove();
      }
    } catch (e) {}
  }).catch(() => {});
}

function markAllNotifications() {
  fetch("{{ route('notificacoes.todas') }}", {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': window.CSRFToken,
      'Accept': 'application/json'
    }
  }).then(() => {
    const badge = document.getElementById('notifCountBadge');
    if (badge) {
      badge.textContent = '0';
      const li = document.querySelector('li.notif-dropdown');
      if (li) li.classList.remove('notif-pulse');
    }
    // Limpa estilos de não lidas no dropdown
    document.querySelectorAll('.dropdown-item-unread').forEach(function(el){
      el.classList.remove('dropdown-item-unread');
      el.classList.remove('fw-bold');
      const dotIcon = el.querySelector('i.fa-dot-circle');
      if (dotIcon) dotIcon.remove();
      const novaBadgeWarning = el.querySelector('.badge.badge-warning');
      if (novaBadgeWarning) novaBadgeWarning.remove();
      const novaBadgeInfo = el.querySelector('.badge.badge-info');
      if (novaBadgeInfo) novaBadgeInfo.remove();
      const btn = el.querySelector('.btn.btn-sm.btn-outline-success');
      if (btn) btn.remove();
    });
  }).catch(() => {});
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.notif-item').forEach(function(el) {
    el.addEventListener('click', function(ev) {
      const id = this.dataset.id;
      const link = this.dataset.link || '#';
      // Marca como lida e navega em seguida
      if (id) markNotificationRead(id);
      // Delay pequeno para evitar bloquear navegação
      setTimeout(function(){ window.location.href = link; }, 50);
      ev.preventDefault();
    });
  });
});
</script>
@endpush
