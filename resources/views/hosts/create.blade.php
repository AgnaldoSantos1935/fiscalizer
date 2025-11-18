@extends('layouts.app')

@section('title', 'Cadastrar Host')

@section('content')
@section('breadcrumb')
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
      <li class="breadcrumb-item"><a href="{{ route('hosts.index') }}" class="text-decoration-none text-primary fw-semibold"><i class="fas fa-server me-1"></i> Hosts</a></li>
      <li class="breadcrumb-item active text-secondary fw-semibold">Novo Host</li>
    </ol>
  </nav>
@endsection
<div class="container">
    <h3 class="mb-4">Cadastrar Novo Host</h3>

    <form action="{{ route('hosts.store') }}" method="POST">
        @csrf
        @include('hosts._form')
        <div class="mt-3 d-flex justify-content-end gap-2">
          <a href="{{ route('hosts.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Cancelar</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Salvar</button>
        </div>
    </form>
</div>
@endsection
@section('js')
<script src="/js/hosts-form.js"></script>
@endsection
