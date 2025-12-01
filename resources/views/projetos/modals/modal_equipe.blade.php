<div class="modal fade" id="modalEquipe" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Cadastrar Membro da Equipe</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEquipe" method="POST" action="{{ route('equipe.store') }}">
        @csrf
        <input type="hidden" name="projeto_id" value="{{ $projeto->id }}">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Pessoa</label>
              <select name="pessoa_id" class="form-select">
                @foreach($pessoas as $p)
                  <option value="{{ $p->id }}">{{ $p->nome_completo }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
<label class="form-label fw-semibold">Perfil</label>
<input type="text" name="perfil" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Horas Previstas</label>
              <input type="number" step="0.5" name="horas_previstas" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-1"></i>Cadastrar Membro</button>
        </div>
      </form>
    </div>
  </div>
</div>
