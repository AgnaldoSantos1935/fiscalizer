@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h4 class="text-secondary fw-semibold">
        <i class="fas fa-history text-primary me-2"></i> Histórico - {{ $monitoramento->nome }}
      </h4>
      <a href="{{ route('monitoramentos.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Voltar
      </a>
    </div>
    <div class="card-body">
      <div class="mb-4">
        <h6 class="fw-semibold text-secondary">Resumo</h6>
        <p class="mb-1"><strong>Uptime:</strong> {{ $uptime }}%</p>
        <p class="mb-1"><strong>Latência média:</strong> {{ $mediaLatencia ? number_format($mediaLatencia,2).' ms' : '—' }}</p>
        <p class="mb-1"><strong>Último teste:</strong> {{ $logs->first()->dataFormatada ?? '—' }}</p>
      </div>

      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Data</th>
            <th>Status</th>
            <th>Latência</th>
            <th>HTTP</th>
            <th>Erro</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
          <tr>
            <td>{{ $log->dataFormatada }}</td>
            <td>{!! $log->online ? '<span class="badge bg-success">Online</span>' : '<span class="badge bg-danger">Offline</span>' !!}</td>
            <td>{{ $log->latencia ? number_format($log->latencia,2).' ms' : '—' }}</td>
            <td>{{ $log->status_code ?? '—' }}</td>
            <td>{{ $log->erro ? Str::limit($log->erro, 40) : '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted">Sem registros recentes</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
