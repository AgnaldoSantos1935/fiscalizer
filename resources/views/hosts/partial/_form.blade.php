@php
    // Campos com valores padrão quando $host não existe
    $isEdit = isset($host);
@endphp

<div class="row g-3">

    {{-- Nome --}}
    <div class="col-md-6">
        <label class="form-label">Nome da Conexão</label>
        <input type="text" name="nome_conexao" class="form-control"
               value="{{ old('nome_conexao', $host->nome_conexao ?? '') }}" required>
    </div>

    {{-- Descrição --}}
    <div class="col-md-6">
        <label class="form-label">Descrição</label>
        <input type="text" name="descricao" class="form-control"
               value="{{ old('descricao', $host->descricao ?? '') }}">
    </div>

    {{-- Provedor --}}
    <div class="col-md-4">
        <label class="form-label">Provedor</label>
        <input type="text" name="provedor" class="form-control"
               value="{{ old('provedor', $host->provedor ?? '') }}">
    </div>

    {{-- Tecnologia --}}
    <div class="col-md-4">
        <label class="form-label">Tecnologia</label>
        <input type="text" name="tecnologia" class="form-control"
               value="{{ old('tecnologia', $host->tecnologia ?? '') }}">
    </div>

    {{-- IP atingível --}}
    <div class="col-md-4">
        <label class="form-label">IP Atingível</label>
        <input type="text" name="ip_atingivel" class="form-control"
               value="{{ old('ip_atingivel', $host->ip_atingivel ?? '') }}">
    </div>

    {{-- Tipo monitoramento --}}
    <div class="col-md-4">
        <label class="form-label">Tipo de Monitoramento</label>
        <select name="tipo_monitoramento" class="form-select">
            @foreach (['ping','porta','http','snmp','mikrotik','speedtest'] as $tipo)
                <option value="{{ $tipo }}"
                    {{ old('tipo_monitoramento', $host->tipo_monitoramento ?? '') === $tipo ? 'selected' : '' }}>
                    {{ ucfirst($tipo) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Host alvo --}}
    <div class="col-md-4">
        <label class="form-label">Host Alvo (IP/URL)</label>
        <input type="text" name="host_alvo" class="form-control" required
               value="{{ old('host_alvo', $host->host_alvo ?? '') }}">
    </div>

    {{-- Porta --}}
    <div class="col-md-4">
        <label class="form-label">Porta</label>
        <input type="number" name="porta" class="form-control"
               value="{{ old('porta', $host->porta ?? '') }}">
    </div>

    {{-- SNMP --}}
    <h5 class="mt-4">SNMP</h5>
    <div class="col-md-6">
        <label class="form-label">Community SNMP</label>
        <input type="text" name="snmp_community" class="form-control"
               value="{{ old('snmp_community', $host->snmp_community ?? '') }}">
    </div>

    {{-- Mikrotik --}}
    <h5 class="mt-4">Mikrotik API</h5>
    <div class="col-md-6">
        <label class="form-label">Usuário Mikrotik</label>
        <input type="text" name="mikrotik_user" class="form-control"
               value="{{ old('mikrotik_user', $host->mikrotik_user ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Senha Mikrotik</label>
        <input type="password" name="mikrotik_pass" class="form-control"
               value="{{ old('mikrotik_pass', $host->mikrotik_pass ?? '') }}">
    </div>

    {{-- JSON extra --}}
    <div class="col-12 mt-4">
        <label class="form-label">Config Extra (JSON)</label>
        <textarea name="config_extra" class="form-control" rows="4">{{ old('config_extra', $host->config_extra ?? '') }}</textarea>
    </div>

    {{-- Botão --}}
    <div class="col-12 mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>{{ $isEdit ? 'Atualizar Host' : 'Salvar Host' }}
        </button>
    </div>

</div>
