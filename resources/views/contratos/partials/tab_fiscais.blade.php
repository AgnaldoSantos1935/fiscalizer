<div class="row">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Fiscal Técnico</label>
        @php $pessoaIdAuth = \App\Models\Pessoa::where('user_id', Auth::id())->value('id'); $ehGestorAtual = (Auth::user()?->role_id === 1) || ($pessoaIdAuth && $pessoaIdAuth === $contrato->gestor_id); @endphp
        @if($ehGestorAtual)
            <select name="fiscal_tecnico_id" class="form-select">
                <option value="">-- Selecione --</option>
                @foreach($pessoas as $p)
                    <option value="{{ $p->id }}" {{ $contrato->fiscal_tecnico_id == $p->id ? 'selected' : '' }}>
                        {{ $p->nome_completo }}
                    </option>
                @endforeach
            </select>
        @else
            @php $nomeTec = optional($contrato->fiscalTecnico)->nome_completo; @endphp
            <input type="text" class="form-control" value="{{ $nomeTec ?: '—' }}" disabled>
        @endif
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Suplente (Fiscal Técnico)</label>
        @php
            $pessoaIdAuth = \App\Models\Pessoa::where('user_id', Auth::id())->value('id');
            $ehGestorAtual = (Auth::user()?->role_id === 1) || ($pessoaIdAuth && $pessoaIdAuth === $contrato->gestor_id);
        @endphp
        @if($ehGestorAtual)
            <select name="suplente_fiscal_tecnico_id" class="form-select">
                <option value="">-- Selecione --</option>
                @foreach($pessoas as $p)
                    <option value="{{ $p->id }}" {{ $contrato->suplente_fiscal_tecnico_id == $p->id ? 'selected' : '' }}>
                        {{ $p->nome_completo }}
                    </option>
                @endforeach
            </select>
        @else
            @php $nomeSupTec = optional($contrato->suplenteFiscalTecnico)->nome_completo; @endphp
            <input type="text" class="form-control" value="{{ $nomeSupTec ?: '—' }}" disabled>
        @endif
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Fiscal Administrativo</label>
        @php $pessoaIdAuth = \App\Models\Pessoa::where('user_id', Auth::id())->value('id'); $ehGestorAtual = (Auth::user()?->role_id === 1) || ($pessoaIdAuth && $pessoaIdAuth === $contrato->gestor_id); @endphp
        @if($ehGestorAtual)
            <select name="fiscal_administrativo_id" class="form-select">
                <option value="">-- Selecione --</option>
                @foreach($pessoas as $p)
                    <option value="{{ $p->id }}" {{ $contrato->fiscal_administrativo_id == $p->id ? 'selected' : '' }}>
                        {{ $p->nome_completo }}
                    </option>
                @endforeach
            </select>
        @else
            @php $nomeAdm = optional($contrato->fiscalAdministrativo)->nome_completo; @endphp
            <input type="text" class="form-control" value="{{ $nomeAdm ?: '—' }}" disabled>
        @endif
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Suplente (Fiscal Administrativo)</label>
        @php
            $pessoaIdAuth = \App\Models\Pessoa::where('user_id', Auth::id())->value('id');
            $ehGestorAtual = (Auth::user()?->role_id === 1) || ($pessoaIdAuth && $pessoaIdAuth === $contrato->gestor_id);
        @endphp
        @if($ehGestorAtual)
            <select name="suplente_fiscal_administrativo_id" class="form-select">
                <option value="">-- Selecione --</option>
                @foreach($pessoas as $p)
                    <option value="{{ $p->id }}" {{ $contrato->suplente_fiscal_administrativo_id == $p->id ? 'selected' : '' }}>
                        {{ $p->nome_completo }}
                    </option>
                @endforeach
            </select>
        @else
            @php $nomeSupAdm = optional($contrato->suplenteFiscalAdministrativo)->nome_completo; @endphp
            <input type="text" class="form-control" value="{{ $nomeSupAdm ?: '—' }}" disabled>
        @endif
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Gestor do Contrato</label>
        @if(Auth::user()?->role_id === 1)
            <select name="gestor_id" class="form-select">
                <option value="">-- Selecione --</option>
                @foreach($pessoas as $p)
                    <option value="{{ $p->id }}" {{ $contrato->gestor_id == $p->id ? 'selected' : '' }}>
                        {{ $p->nome_completo }}
                    </option>
                @endforeach
            </select>
        @else
            @php $nomeGestor = optional($contrato->gestor)->nome_completo; @endphp
            <input type="text" class="form-control" value="{{ $nomeGestor ?: '—' }}" disabled>
        @endif
    </div>
</div>
