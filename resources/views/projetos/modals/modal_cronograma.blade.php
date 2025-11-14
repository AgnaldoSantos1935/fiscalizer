<div class="modal fade" id="modalCronograma" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title"><i class="fas fa-calendar-alt me-2"></i>Nova Etapa do Cronograma</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formCronograma" method="POST" action="{{ route('cronograma.store') }}">
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
              <input type="text" name="responsavel" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Data Início</label>
              <input type="date" name="data_inicio" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Data Fim</label>
              <input type="date" name="data_fim" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Status</label>
              <select name="status" class="form-select">
                <option>Planejado</option>
                <option>Em andamento</option>
                <option>Concluído</option>
                <option>Paralisado</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i>Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
