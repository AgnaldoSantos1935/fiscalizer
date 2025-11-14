<div class="d-flex justify-content-between align-items-center mb-2">
  <h6 class="text-secondary fw-semibold mb-0"><i class="fas fa-plus-circle text-primary me-2"></i>Requisitos</h6>
  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalRequisito">
    <i class="fas fa-plus me-1"></i>Adicionar Requisito
  </button>
</div>
@include('projetos.modals.modal_requisito')

<!-- Repita para Atividades -->
<div class="d-flex justify-content-between align-items-center mb-2">
  <h6 class="text-secondary fw-semibold mb-0"><i class="fas fa-wrench text-success me-2"></i>Atividades Técnicas</h6>
  <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAtividade">
    <i class="fas fa-plus me-1"></i>Adicionar Atividade
  </button>
</div>
@include('projetos.modals.modal_atividade')

<!-- Cronograma -->
<div class="d-flex justify-content-between align-items-center mb-2">
  <h6 class="text-secondary fw-semibold mb-0"><i class="fas fa-calendar-alt text-warning me-2"></i>Cronograma</h6>
  <button class="btn btn-sm btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalCronograma">
    <i class="fas fa-plus me-1"></i>Adicionar Etapa
  </button>
</div>
@include('projetos.modals.modal_cronograma')

<!-- Equipe -->
<div class="d-flex justify-content-between align-items-center mb-2">
  <h6 class="text-secondary fw-semibold mb-0"><i class="fas fa-users text-info me-2"></i>Equipe</h6>
  <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalEquipe">
    <i class="fas fa-user-plus me-1"></i>Adicionar Membro
  </button>
</div>
@include('projetos.modals.modal_equipe')
@section('js')
<script>
  window.projetoId = {{ $projeto->id }};
</script>
<script type="module" src="{{ mix('resources/js/projetos.js') }}"></script>

@endsection
