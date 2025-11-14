@extends('layouts.app')

@section('title', 'Cadastrar Host')

@section('content')
<div class="container">
    <h3 class="mb-4">Cadastrar Novo Host</h3>

    <form action="{{ route('hosts.store') }}" method="POST">
        @csrf
        @include('hosts._form') {{-- PARTIAL --}}
    </form>
</div>
@endsection
@section('js')
<script src="/js/hosts-form.js"></script>
@endsection
