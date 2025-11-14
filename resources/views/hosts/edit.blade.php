@extends('layouts.app')

@section('title', 'Editar Host')

@section('content')
<div class="container">
    <h3 class="mb-4">Editar Host</h3>

    <form action="{{ route('hosts.update', $host->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('hosts._form', ['host' => $host]) {{-- PARTIAL --}}
    </form>
</div>
@endsection
@section('js')
<script src="/js/hosts-form.js"></script>
@endsection
