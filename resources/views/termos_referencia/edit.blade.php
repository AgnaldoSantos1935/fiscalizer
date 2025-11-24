@extends('layouts.app')
@section('plugins.Summernote', true)
@section('title', 'Editar Termo de Referência')

@section('content_body')
<div class="container-fluid">
    @php
        $user = auth()->user();
        $profile = \App\Models\UserProfile::where('user_id', $user?->id)->first();
        $pessoa = optional($user)->pessoa;
        $session_responsavel = $profile->nome_completo ?? $user?->name ?? '';
        $session_cargo = $profile->cargo ?? (optional($user?->role)->nome ?? '');
        $session_matricula = $profile->matricula ?? '';
        $session_cidade = $pessoa->cidade ?? ($profile->cidade ?? 'Belém');
    @endphp
    <form action="{{ route('contratacoes.termos-referencia.update', $tr) }}" method="POST" class="card shadow-sm border-0 rounded-4">
        @csrf
        @method('PUT')
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-edit text-warning me-2"></i>Editar Termo de Referência
            </h4>
            <a href="{{ route('contratacoes.termos-referencia.show', $tr) }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
        </div>
        <div class="card-body bg-white">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tipo de TR</label>
                    <select name="tipo_tr" class="form-select">
                        <option value="">Selecione...</option>
                        <option value="bens_comuns" @selected(old('tipo_tr', $tr->tipo_tr)==='bens_comuns')>Bens Comuns</option>
                        <option value="servicos_sem_mao_de_obra_sem_prorrogacao" @selected(old('tipo_tr', $tr->tipo_tr)==='servicos_sem_mao_de_obra_sem_prorrogacao')>Serviços sem Mão-de-Obra e sem Prorrogação</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">PAE nº</label>
                    <input type="text" name="pae_numero" class="form-control" value="{{ old('pae_numero', $tr->pae_numero) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cidade</label>
                    <input type="text" name="cidade" class="form-control" value="{{ old('cidade', $tr->cidade ?? $session_cidade) }}" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Data de Emissão</label>
                    <input type="date" name="data_emissao" class="form-control" value="{{ old('data_emissao', $tr->data_emissao) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Responsável</label>
                    <input type="text" name="responsavel_nome" class="form-control" value="{{ old('responsavel_nome', $session_responsavel) }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cargo</label>
                    <input type="text" name="responsavel_cargo" class="form-control" value="{{ old('responsavel_cargo', $session_cargo) }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Matrícula</label>
                    <input type="text" name="responsavel_matricula" class="form-control" value="{{ old('responsavel_matricula', $session_matricula) }}" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" value="{{ old('titulo', $tr->titulo) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Objeto</label>
                <textarea name="objeto" class="form-control tr-editor" rows="10">{{ old('objeto', $tr->objeto) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Justificativa</label>
                <textarea name="justificativa" class="form-control tr-editor" rows="10">{{ old('justificativa', $tr->justificativa) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Escopo</label>
                <textarea name="escopo" class="form-control tr-editor" rows="10">{{ old('escopo', $tr->escopo) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Requisitos</label>
                <textarea name="requisitos" class="form-control tr-editor" rows="10">{{ old('requisitos', $tr->requisitos) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Critérios de Julgamento</label>
                <textarea name="criterios_julgamento" class="form-control tr-editor" rows="10">{{ old('criterios_julgamento', $tr->criterios_julgamento) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Prazos</label>
                <textarea name="prazos" class="form-control tr-editor" rows="10">{{ old('prazos', $tr->prazos) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Local de Execução</label>
                <textarea name="local_execucao" class="form-control tr-editor" rows="10">{{ old('local_execucao', $tr->local_execucao) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Forma de Pagamento</label>
                <textarea name="forma_pagamento" class="form-control tr-editor" rows="10">{{ old('forma_pagamento', $tr->forma_pagamento) }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Valor Estimado</label>
                    <input type="number" step="0.01" name="valor_estimado" value="{{ old('valor_estimado', $tr->valor_estimado) }}" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="rascunho" @selected(old('status', $tr->status) === 'rascunho')>Rascunho</option>
                        <option value="em_analise" @selected(old('status', $tr->status) === 'em_analise')>Em análise</option>
                        <option value="finalizado" @selected(old('status', $tr->status) === 'finalizado')>Finalizado</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Salvar Alterações</button>
        </div>
    </form>
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection

@push('css')
@endpush

@push('js')
@endpush