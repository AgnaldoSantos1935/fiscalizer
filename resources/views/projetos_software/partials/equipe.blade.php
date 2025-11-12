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
    <table id="tabelaEquipe" class="table table-striped table-hover mb-0 w-100">
      <thead class="table-light">
        <tr>
          <th>Nome</th>
          <th>Papel</th>
          <th class="text-end">Horas Previstas</th>
          <th class="text-end">Horas Realizadas</th>
          <th class="text-center" width="100">Ações</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

@include('projetos.modals.modal_equipe')
