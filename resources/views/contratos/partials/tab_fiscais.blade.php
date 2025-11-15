<div class="row">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Fiscal Técnico</label>
        <select name="fiscal_tecnico_id" class="form-select">
            <option value="">-- Selecione --</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}"
                    @if($contrato->fiscaisTecnicos->contains('id', $u->id)) selected @endif>
                    {{ $u->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Fiscal Administrativo</label>
        <select name="fiscal_administrativo_id" class="form-select">
            <option value="">-- Selecione --</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}"
                    @if($contrato->fiscaisAdministrativos->contains('id', $u->id)) selected @endif>
                    {{ $u->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Gestor do Contrato</label>
        <select name="gestor_id" class="form-select">
            <option value="">-- Selecione --</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}"
                    @if($contrato->gestores->contains('id', $u->id)) selected @endif>
                    {{ $u->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
