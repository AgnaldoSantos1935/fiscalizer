<div class="row">
  <div class="col-md-3 mb-3">
    <label class="form-label">Uptime (%)</label>
    <input type="number" step="0.01" min="0" max="100" name="itens[0][uptime_percent]" class="form-control" required>
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label">Downtime (min)</label>
    <input type="number" step="1" min="0" name="itens[0][downtime_minutos]" class="form-control" required>
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label">Quedas</label>
    <input type="number" step="1" min="0" name="itens[0][qtd_quedas]" class="form-control" required>
  </div>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Valor Mensal Contratado (R$)</label>
    <input type="text" name="itens[0][valor_mensal_contratado]" class="form-control money-br-input" required>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Desconto (R$)</label>
    <input type="text" name="itens[0][valor_desconto]" class="form-control money-br-input">
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Valor Final (R$)</label>
    <input type="text" name="itens[0][valor_final]" class="form-control money-br-input">
  </div>
</div>

