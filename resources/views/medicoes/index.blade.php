@extends('layouts.app')
@section('title', 'Medições')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Medições (APF)</h2>
  <a href="{{ route('medicoes.create') }}" class="btn btn-primary">+ Nova Medição</a>
</div>

<table class="table table-bordered table-striped align-middle">
  <thead class="table-dark">
    <tr>
      <th>Contrato</th>
      <th>Mês Ref.</th>
      <th>Total PF</th>
      <th>Valor Unitário (R$)</th>
      <th>Valor Total (R$)</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($medicoes as $m)
      <tr>
        <td>{{ $m->contrato->numero }}</td>
        <td>{{ $m->mes_referencia }}</td>
        <td>{{ $m->total_pf }}</td>
        <td>{{ number_format($m->valor_unitario_pf,2,',','.') }}</td>
        <td>{{ number_format($m->valor_total,2,',','.') }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
@endsection
