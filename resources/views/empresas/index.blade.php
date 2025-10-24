@extends('layouts.app')
@section('title', 'Empresas')

@section('content')

<br>
<div class="card">

  <div class="card-header">

    <div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Empresas Cadastradas</h2>
  <a href="{{ route('empresas.create') }}" class="btn btn-primary">+ Nova Empresa</a>
</div>
  </div>
  <div class="card-body">

<table class="table table-bordered table-striped align-middle">
  <thead class="table-ligth">
    <tr>

      <th>Razão Social</th>
      <th>CNPJ</th>
      <th>Email</th>
      <th>Telefone</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($empresas as $empresa)
      <tr>
        <td>{{ $empresa->razao_social }}</td>
        <td>{{ $empresa->cnpj }}</td>
        <td>{{ $empresa->email }}</td>
        <td>{{ $empresa->telefone }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="5" class="text-center text-muted">Nenhuma empresa cadastrada.</td>
      </tr>
    @endforelse
  </tbody>
</table>

  </div>
</div>


@endsection
