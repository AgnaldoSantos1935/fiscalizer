@extends('layouts.app')

@section('title', 'Editar Conexão')

@section('content_header')
<h1>
    <i class="fas fa-network-wired me-2 text-primary"></i>
    Editar Conexão
</h1>
@stop

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <form id="formHost" action="{{ route('hosts.update', $host->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <!-- 🔹 Contrato -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Contrato</label>
                    <select id="contratoSelect" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($contratos as $contrato)
                            <option value="{{ $contrato->id }}"
                                {{ optional($host->itemContrato->contrato)->id == $contrato->id ? 'selected' : '' }}>
                                {{ $contrato->numero }} - {{ Str::limit($contrato->objeto, 60) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 🔹 Item Contratual -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Item Contratual</label>
                    <select name="itemcontratado" id="itemSelect" class="form-select" required>
                        <option value="">Carregando...</option>
                        @foreach($itens as $item)
                            <option value="{{ $item->id }}"
                                {{ $host->itemcontratado == $item->id ? 'selected' : '' }}>
                                {{ $item->descricao_item }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 🔹 Escola -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Escola / Setor</label>
                    <select name="local" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($escolas as $escola)
                            <option value="{{ $escola->id_escola }}"
                                {{ $host->local == $escola->id_escola ? 'selected' : '' }}>
                                {{ $escola->nome }} - {{ $escola->municipio }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr class="my-4">

                <!-- 🔹 Nome e descrição -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nome da Conexão</label>
                    <input type="text" name="nome_conexao" class="form-control"
                        value="{{ old('nome_conexao', $host->nome_conexao) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Descrição</label>
                    <input type="text" name="descricao" class="form-control"
                        value="{{ old('descricao', $host->descricao) }}">
                </div>

                <!-- 🔹 Provedor, tecnologia e porta -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Provedor</label>
                    <input type="text" name="provedor" class="form-control"
                        value="{{ old('provedor', $host->provedor) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tecnologia</label>
                    <select name="tecnologia" class="form-select">
                        <option value="">Selecione...</option>
                        @foreach(['fibra', 'rádio', 'satélite', '4g'] as $tec)
                            <option value="{{ $tec }}" {{ $host->tecnologia === $tec ? 'selected' : '' }}>
                                {{ ucfirst($tec) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Porta</label>
                    <input type="number" name="porta" class="form-control"
                        value="{{ old('porta', $host->porta) }}">
                </div>

                <!-- 🔹 IP e status -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">IP Atingível</label>
                    <input type="text" name="ip_atingivel" class="form-control"
                        value="{{ old('ip_atingivel', $host->ip_atingivel) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="ativo" {{ $host->status === 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ $host->status === 'inativo' ? 'selected' : '' }}>Inativo</option>
                        <option value="em manutenção" {{ $host->status === 'em manutenção' ? 'selected' : '' }}>Em manutenção</option>
                    </select>
                </div>

            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Atualizar Conexão
                </button>
                <a href="{{ route('hosts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
            </div>

        </form>
    </div>
</div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const contratoSelect = document.getElementById('contratoSelect');
    const itemSelect = document.getElementById('itemSelect');
    const currentItemId = "{{ $host->itemcontratado }}";

    async function carregarItens(contratoId, selectedId = null) {
        itemSelect.innerHTML = '<option value="">Carregando...</option>';

        if (!contratoId) {
            itemSelect.innerHTML = '<option value="">Selecione o contrato primeiro...</option>';
            return;
        }

        try {
            const res = await fetch(`/api/contratos/${contratoId}/itens`);
            const itens = await res.json();

            itemSelect.innerHTML = '<option value="">Selecione...</option>';
            itens.forEach(item => {
                const selected = (item.id == selectedId) ? 'selected' : '';
                itemSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.descricao_item}</option>`;
            });
        } catch (err) {
            console.error('Erro ao carregar itens:', err);
            itemSelect.innerHTML = '<option value="">Erro ao carregar itens</option>';
        }
    }

    // carregar ao abrir
    const contratoIdInicial = contratoSelect.value;
    if (contratoIdInicial) {
        await carregarItens(contratoIdInicial, currentItemId);
    }

    // carregar ao trocar
    contratoSelect.addEventListener('change', function() {
        carregarItens(this.value, null);
    });
});
</script>
@stop
