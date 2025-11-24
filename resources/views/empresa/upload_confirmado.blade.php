@extends('layouts.publico')
@section('title', 'Documento Recebido – Fiscalizer')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container py-5 text-center">
  <img src="/images/brasao-pa.png" width="80" class="mb-3">
  <h3 class="text-success fw-bold">Documento Recebido!</h3>
  <p class="mt-3">Seu documento técnico referente à demanda <strong>#{{ $demanda->id }}</strong> foi recebido com sucesso.</p>
  <p class="text-muted">A análise automática (IA) será iniciada imediatamente. A DETEC entrará em contato caso sejam necessárias correções.</p>
</div>
@endsection