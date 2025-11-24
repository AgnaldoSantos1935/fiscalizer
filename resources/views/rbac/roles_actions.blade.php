@extends('layouts.app')

@section('title', 'Gestão de Permissões (Perfis × Ações)')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h4 class="fw-bold">
            <i class="fas fa-user-shield text-primary"></i>
            Perfis × Ações
        </h4>
        <a href="{{ route('actions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-list"></i> Lista de Ações
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('rbac.roles_actions.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Perfil</label>
                    <select name="role_id" class="form-select" onchange="this.form.submit()">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ ($selectedRole && $selectedRole->id === $r->id) ? 'selected' : '' }}>
                                {{ $r->nome ?? ('Role #'.$r->id) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($selectedRole)
        <form method="POST" action="{{ route('rbac.roles_actions.update', $selectedRole->id) }}">
            @csrf

            @foreach($actions as $modulo => $lista)
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">
                            <i class="fas fa-layer-group me-2 text-primary"></i>
                            {{ ucfirst($modulo ?: 'Geral') }}
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleModulo('{{ $modulo }}')">
                            Marcar/Desmarcar
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row" id="modulo-{{ $modulo }}">
                            @foreach($lista as $a)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="actions[]" value="{{ $a->id }}"
                                               id="action-{{ $a->id }}"
                                               {{ $selectedRole->actions->contains('id', $a->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="action-{{ $a->id }}">
                                            <span class="fw-semibold">{{ $a->codigo }}</span>
                                            <small class="text-muted d-block">{{ $a->nome }}</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Permissões
                </button>
                <a href="{{ route('rbac.roles_actions.index', ['role_id' => $selectedRole->id]) }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    @else
        <div class="alert alert-info">Nenhum perfil selecionado.</div>
    @endif
</div>

<script>
function toggleModulo(modulo) {
    const container = document.getElementById('modulo-' + modulo);
    if (!container) return;
    const inputs = container.querySelectorAll('input[type="checkbox"]');
    const hasUnchecked = Array.from(inputs).some(i => !i.checked);
    inputs.forEach(i => i.checked = hasUnchecked);
}
</script>
@endsection