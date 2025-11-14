<div class="modal fade" id="modalAtividade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-wrench me-2"></i>Nova Atividade Técnica</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAtividade" method="POST" action="{{ route('atividades.store') }}">
        @csrf
        <input type="hidden" name="projeto_id" value="{{ $projeto->id }}">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Etapa</label>
              <input type="text" name="etapa" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Responsável</label>
              <input type="text" name="analista" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Data</label>
              <input type="date" name="data" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Horas Trabalhadas</label>
              <input type="number" name="horas" step="0.5" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Descrição</label>
              <input type="text" name="descricao" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
