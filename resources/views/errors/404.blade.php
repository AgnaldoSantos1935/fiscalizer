@extends('adminlte::page')

@section('title', 'Página não encontrada')

@section('content_header')
    <h1 class="text-warning"><i class="fas fa-exclamation-circle"></i> Página não encontrada</h1>
@stop

@section('content')
<div class="d-flex align-items-center justify-content-center" style="height:70vh;">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-search fa-5x text-warning"></i>
        </div>

        <h2 class="fw-bold text-warning">404 - Página não encontrada</h2>

        <p class="mt-3 text-muted">
            O recurso solicitado não foi localizado.<br>
            Verifique o endereço ou entre em contato com o administrador do sistema.
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
