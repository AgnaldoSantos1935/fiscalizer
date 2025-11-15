@extends('layouts.publico')

@section('title', 'Envio de Documento Técnico – Fiscalizer')

@section('content')
<div class="container py-5">

    <div class="text-center mb-4">
        <img src="/images/brasao-pa.png" alt="Brasão do Pará" width="90">
        <h3 class="mt-3 fw-bold text-primary">Envio de Documento Técnico</h3>
        <p class="text-muted">
            Demanda Nº {{ $demanda->id }} – {{ $demanda->titulo }}
        </p>
    </div>

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">

            <h5 class="fw-semibold mb-3">Instruções para Envio</h5>

            <ul>
                <li>Envie apenas um arquivo consolidado (PDF, DOCX, ZIP ou XLSX).</li>
                <li>O documento deve conter: requisitos, cronograma, protótipos, PF/UST.</li>
                <li>Tamanho máximo permitido: <strong>50 MB</strong>.</li>
            </ul>

            <form action="{{ route('empresa.upload_documento_post', $token) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold">Selecione o Documento Técnico</label>
                    <input type="file" class="form-control form-control-lg" name="documento" required>
                </div>

                <button class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-upload me-2"></i>
                    Enviar Documento
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
