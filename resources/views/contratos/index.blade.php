@extends('layouts.app')
@section('title', 'Contratos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Contratos</h2>
  <a href="{{ route('contratos.create') }}" class="btn btn-primary">+ Novo Contrato</a>
</div>

<table class="table table-bordered table-striped align-middle">
  <thead class="table-dark">
    <tr>
      <th>Número</th>
      <th>Objeto</th>
      <th>Empresa Contratada</th>
      <th>Valor Global (R$)</th>
      <th>Data Início</th>
      <th>Data Fim</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($contratos as $contrato)
      <tr>
        <td>{{ $contrato->numero }}</td>
        <td>{{ Str::limit($contrato->objeto, 60) }}</td>
        <td>{{ $contrato->contratada->razao_social ?? '-' }}</td>
        <td>{{ number_format($contrato->valor_global, 2, ',', '.') }}</td>
        <td>{{ $contrato->data_inicio }}</td>
        <td>{{ $contrato->data_fim }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="text-center text-muted">Nenhum contrato cadastrado.</td>
      </tr>
    @endforelse
  </tbody>
</table>
@endsection
