@extends('layouts.app')
@section('title','Ata')
@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <h3>Ata {{ $ata->numero }}</h3>
  <div class="mb-3">Objeto: {{ $ata->objeto }}</div>
  <div class="mb-3">Vigência: {{ $ata->vigencia_inicio?->format('d/m/Y') }} — {{ optional($ata->vigencia_final)?->format('d/m/Y') }} ({{ $ata->situacao }})</div>
  <a href="{{ route('atas.edit',$ata->id) }}" class="btn btn-primary">Editar</a>
</div>
@endsection

