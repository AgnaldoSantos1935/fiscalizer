<div class="modal fade" id="modalRequisito" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-list me-2"></i>Novo Requisito</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formRequisito" method="POST" action="{{ route('requisitos.store') }}">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="projeto_id" value="{{ $projeto->id }}">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Descrição</label>
              <input type="text" name="descricao" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tipo</label>
              <select name="tipo" class="form-select">
                <option>EE</option><option>SE</option><option>CE</option><option>ALI</option><option>AIE</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Complexidade</label>
              <select name="complexidade" class="form-select">
                <option>Baixa</option><option>Média</option><option>Alta</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Pontos de Função</label>
              <input type="number" name="pontos_funcao" step="0.01" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Responsável</label>
              <input type="text" name="responsavel" class="form-control">
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
