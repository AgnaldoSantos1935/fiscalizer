@extends('adminlte::page')

@section('title', 'Erro interno')

@section('content_header')
    <h1 class="text-danger"><i class="fas fa-bug"></i> Erro interno do sistema</h1>
@stop

@section('content')
<div class="d-flex align-items-center justify-content-center" style="height:70vh;">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-server fa-5x text-danger"></i>
        </div>

        <h2 class="fw-bold text-danger">500 - Erro interno</h2>

        <p class="mt-3 text-muted">
            Ocorreu um problema inesperado durante o processamento.<br>
            Nossa equipe técnica foi notificada (ou entre em contato com o administrador).
        </p>

        <a href="{{ route('home.index') }}" class="btn btn-primary mt-4 shadow-sm">
            <i class="fas fa-home"></i> Voltar ao Painel
        </a>
    </div>
</div>
@stop

@section('css')
    <style>
        .content-header h1 {
            font-weight: 600;
        }
    </style>
@stop
