<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Descrição</label>
    <input type="text" name="item[descricao]" class="form-control" placeholder="Medição mensal" required>
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Chamados Pendentes</label>
    <input type="number" name="item[chamados_pendentes]" class="form-control" value="0" min="0">
  </div>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Valor Mensal Contratado (R$)</label>
    <input type="text" name="item[valor_mensal_contratado]" class="form-control money-br-input" required>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Desconto (R$)</label>
    <input type="text" name="item[valor_desconto]" class="form-control money-br-input" value="0">
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Valor Final (R$)</label>
    <input type="text" name="item[valor_final]" class="form-control money-br-input">
  </div>
</div>

