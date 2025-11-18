@extends('layouts.app')
@section('title','Atas de Registro de Preços')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between mb-3">
    <h3>Atas de Registro de Preços</h3>
    <a href="{{ route('atas.create') }}" class="btn btn-primary">Nova Ata</a>
  </div>
  <table class="table table-striped">
    <thead><tr><th>Número</th><th>Órgão Gerenciador</th><th>Fornecedor</th><th>Vigência</th><th>Situação</th><th>Saldo disponível</th><th></th></tr></thead>
    <tbody>
      @foreach($atas as $a)
        <tr>
          <td>{{ $a->numero }}</td>
          <td>{{ $a->orgaoGerenciador->razao_social ?? '—' }}</td>
          <td>{{ $a->fornecedor->razao_social ?? '—' }}</td>
          <td>{{ $a->vigencia_inicio?->format('d/m/Y') }} — {{ optional($a->vigencia_final)?->format('d/m/Y') }}</td>
          <td>{{ $a->situacao }}</td>
          <td>R$ {{ number_format($a->saldo_disponivel ?? 0, 2, ',', '.') }}</td>
          <td><a href="{{ route('atas.edit',$a->id) }}" class="btn btn-sm btn-outline-secondary">Editar</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-3"><a href="{{ route('atas.index') }}" class="btn btn-outline-secondary">Atualizar</a></div>
  <div class="mt-2 text-muted small">Vigência automática atualiza diariamente.</div>
</div>
@endsection
