@extends('layouts.app')
@section('title', 'Contratações')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-file-signature text-primary me-2"></i> Contratações
            </h4>
        </div>
        <div class="card-body bg-white">
            <p class="text-secondary">Módulo para apoiar processos de contratação e elaboração de Termos de Referência.</p>

            <div class="list-group">
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('contratacoes.termos-referencia.index') }}">
                    <span><i class="fas fa-list-ul me-2"></i>Termos de Referência</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('contratacoes.termos-referencia.create') }}">
                    <span><i class="fas fa-plus-circle me-2"></i>Novo Termo de Referência</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="alert alert-info mt-3">
        Assim que você enviar o padrão do documento, adaptarei o formulário para refletir todas as seções e campos obrigatórios conforme o modelo.
    </div>
</div>
@endsection