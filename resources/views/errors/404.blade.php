@extends('layouts.app')

@section('title', 'Acesso Negado')

@section('content')
<div class="text-center mt-5">
    <h1 class="display-4 text-danger"><i class="fas fa-ban"></i> Acesso Negado</h1>
    <p class="lead mt-3">Você não possui permissão para acessar esta página.</p>
    <a href="{{ route('home') }}" class="btn btn-primary mt-3">Voltar ao Início</a>
</div>
@endsection
