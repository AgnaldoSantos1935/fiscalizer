@extends('layouts.app')

@section('title', 'Upload de Documentos da Medição')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-4">
        <h4 class="fw-bold">
            <i class="fas fa-file-upload text-primary"></i>
            Anexar Documentos da Medição
        </h4>

        <a href="{{ route('medicoes.workflow.show', $medicao->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    @can('medicoes_validar')
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="fw-semibold">
                    <i class="fas fa-folder me-2 text-primary"></i>
                    Envio de Arquivos – Fiscal Administrativo
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('medicoes.documentos.upload', $medicao->id) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Selecione os documentos</label>
                        <input type="file" name="documentos[]" multiple required class="form-control">
                        <small class="text-muted">
                            Envie: Nota Fiscal (PDF/XML), Planilha, Relatório, Certidões...
                        </small>
                    </div>

                    <button class="btn btn-primary">
                        <i class="fas fa-upload"></i> Enviar Documentos
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            Permissão negada. É necessário possuir a ação <strong>medicoes_validar</strong>.
        </div>
    @endcan

</div>
@endsection
