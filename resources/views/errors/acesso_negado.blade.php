@extends('adminlte::page')

@section('title', 'Acesso Negado')

@section('content_header')
    <h1 class="text-danger"><i class="fas fa-ban"></i> Acesso Negado</h1>
@stop

@section('content')
@include('layouts.components.breadcrumbs')
<div class="d-flex align-items-center justify-content-center h-70vh">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-exclamation-triangle fa-5x text-danger"></i>
        </div>

        <h2 class="fw-bold text-danger">Ops! Você não tem permissão para acessar esta página.</h2>

        <p class="mt-3 text-muted">
            Seu perfil de acesso não possui as permissões necessárias para visualizar este conteúdo.<br>
            Caso acredite que isto seja um engano, entre em contato com o administrador do sistema.
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
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
        }
    </style>
@stop
