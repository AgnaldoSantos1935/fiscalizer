<div class="card shadow-sm border-0 rounded-4 mb-4">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h6 class="mb-0 text-secondary fw-semibold">
      <i class="fas fa-calendar-alt text-warning me-2"></i>Cronograma
    </h6>
    <button class="btn btn-sm btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalCronograma">
      <i class="fas fa-plus me-1"></i>Adicionar Etapa
    </button>
  </div>

  <div class="card-body p-0">
    <table class="table table-striped table-hover mb-0 w-100">
      <thead class="table-light">
        <tr>
          <th>Etapa</th>
          <th>Responsável</th>
          <th>Início</th>
          <th>Fim</th>
          <th>Status</th>
          <th class="text-center" width="100">Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach(($cronograma ?? []) as $c)
          <tr>
            <td>{{ $c->etapa ?? '—' }}</td>
            <td>{{ $c->responsavel ?? '—' }}</td>
            <td>{{ optional($c->data_inicio)->format('d/m/Y') ?? '—' }}</td>
            <td>{{ optional($c->data_fim)->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $c->status ?? '—' }}</td>
            <td class="text-center">
              <a href="{{ url('cronograma') }}/{{ $c->id }}/edit" class="btn btn-warning btn-sm me-1"><i class="fas fa-edit"></i></a>
              <form action="{{ url('cronograma') }}/{{ $c->id }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir etapa?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@include('projetos.modals.modal_cronograma')
