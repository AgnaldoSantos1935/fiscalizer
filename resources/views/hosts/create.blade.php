@extends('layouts.app')

@section('title', 'Cadastrar Host')

@section('content')
@include('layouts.components.breadcrumbs')
@section('breadcrumb')
  @include('layouts.components.breadcrumbs', [
    'trail' => [
      ['label' => 'Hosts', 'icon' => 'fas fa-server', 'url' => route('hosts.index')],
      ['label' => 'Cadastrar Host']
    ]
  ])
@endsection
<div class="container">
    <h3 class="mb-4">Cadastrar Host</h3>

    <form action="{{ route('hosts.store') }}" method="POST">
        @csrf
        @include('hosts._form')
        <div class="mt-3 d-flex justify-content-end gap-2">
          <a href="{{ route('hosts.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Cancelar</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Cadastrar Host</button>
        </div>
    </form>
</div>
@endsection
@section('js')
<script src="/js/hosts-form.js"></script>
@endsection
