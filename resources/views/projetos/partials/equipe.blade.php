<div class="card shadow-sm border-0 rounded-4 mb-4">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h6 class="mb-0 text-secondary fw-semibold">
      <i class="fas fa-users text-info me-2"></i>Equipe do Projeto
    </h6>
    <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalEquipe">
      <i class="fas fa-user-plus me-1"></i>Adicionar Membro
    </button>
  </div>

  <div class="card-body p-0">
    <table class="table table-striped table-hover mb-0 w-100">
      <thead class="table-light">
        <tr>
          <th>Nome</th>
          <th>Perfil</th>
          <th class="text-end">Horas Previstas</th>
          <th class="text-end">Horas Realizadas</th>
          <th class="text-center" width="100">Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach(($equipe ?? []) as $m)
          <tr>
            <td>{{ optional($m->pessoa)->nome_completo ?? '—' }}</td>
            <td>{{ $m->perfil ?? '—' }}</td>
            <td class="text-end">{{ number_format((float) ($m->horas_previstas ?? 0), 2, ',', '.') }}</td>
            <td class="text-end">{{ number_format((float) ($m->horas_realizadas ?? 0), 2, ',', '.') }}</td>
            <td class="text-center">
              <a href="{{ url('equipe') }}/{{ $m->id }}/edit" class="btn btn-warning btn-sm me-1"><i class="fas fa-edit"></i></a>
              <form action="{{ url('equipe') }}/{{ $m->id }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir membro?');">
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

@include('projetos.modals.modal_equipe')
