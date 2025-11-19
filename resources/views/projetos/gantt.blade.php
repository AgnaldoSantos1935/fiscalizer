@extends('layouts.app')
@section('title','Gantt do Projeto - '.$projeto->nome)

@section('css')
<style>
  .gantt-row { display: flex; align-items: center; margin-bottom: 4px; }
  .gantt-label { width: 220px; font-size: 0.85rem; }
  .gantt-bar-container { flex: 1; position: relative; height: 22px; background:#f8f9fa; border-radius: 4px; overflow:hidden; }
  .gantt-bar { position:absolute; top:2px; bottom:2px; border-radius: 4px; }
</style>
@endsection

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-0">
      <h4 class="text-secondary fw-semibold">
        <i class="fas fa-stream me-2 text-primary"></i>
        Gantt do Projeto – {{ $projeto->nome }}
      </h4>
    </div>
    <div class="card-body">

      @php
        $datas = collect();
        foreach ($itens as $item) {
          if ($item->inicio_planejado) $datas->push($item->inicio_planejado);
          if ($item->fim_planejado)    $datas->push($item->fim_planejado);
        }
        $min = $datas->min();
        $max = $datas->max();
        $totalDias = $min && $max ? $min->diffInDays($max) ?: 1 : 1;
      @endphp

      @forelse($itens as $item)
        @php
          $ini = $item->inicio_planejado ?? $min;
          $fim = $item->fim_planejado    ?? $ini;
          $offset = $min ? $min->diffInDays($ini) : 0;
          $dur    = max($ini->diffInDays($fim), 1);
          $left   = ($offset / $totalDias) * 100;
          $width  = ($dur    / $totalDias) * 100;
        @endphp
        <div class="gantt-row">
          <div class="gantt-label text-truncate" title="{{ $item->titulo }}">
            {{ $item->titulo }}
          </div>
          <div class="gantt-bar-container">
            <div class="gantt-bar bg-primary"
                 style="left:{{ $left }}%; width:{{ $width }}%;"></div>
          </div>
        </div>
      @empty
        <p class="text-muted">Nenhum item com datas planejadas para exibir no Gantt.</p>
      @endforelse

    </div>
  </div>

</div>
@endsection
