@extends('layouts.app')
@section('title', 'Nota de Empenho '.$nota->numero)

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card shadow-sm rounded-4">
    <div class="card-header bg-white d-flex justify-content-between">
      <h4 class="mb-0">Nota de Empenho Nº {{ $nota->numero }}</h4>
      <a href="{{ route('empenhos.imprimir', $nota->id) }}" class="btn btn-outline-primary"><i class="fas fa-file-pdf"></i> Imprimir</a>
    </div>
    <div class="card-body">
      <p><strong>Empresa:</strong> {{ $nota->empresa->razao_social ?? '—' }}</p>
      <p><strong>Contrato:</strong> {{ $nota->contrato->numero ?? '—' }}</p>
      <p><strong>Data:</strong> {{ $nota->data_formatada }}</p>
      <p><strong>Valor Total:</strong> {{ $nota->valor_total_formatado }}</p>

      <hr>
      <h5 class="fw-bold text-secondary mb-3">Itens</h5>
      <table class="table table-sm table-bordered">
        <thead><tr><th>#</th><th>Descrição</th><th>Qtd</th><th>Valor Unit</th><th>Total</th></tr></thead>
        <tbody>
          @foreach($nota->itens as $i => $item)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item->descricao }}</td>
            <td>{{ number_format($item->quantidade,2,',','.') }}</td>
            <td>{{ number_format($item->valor_unitario,2,',','.') }}</td>
            <td>{{ number_format($item->valor_total,2,',','.') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
