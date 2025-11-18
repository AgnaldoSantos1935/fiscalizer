<div class="row">
  <div class="col-12">
    <div class="alert alert-info">Preencha os itens da medição de software abaixo.</div>
  </div>
  <div class="col-12">
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th>Descrição</th>
            <th style="width: 120px;">PF</th>
            <th style="width: 120px;">UST</th>
            <th style="width: 120px;">Horas</th>
            <th style="width: 180px;">Valor Total (R$)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <input type="text" name="itens[0][descricao]" class="form-control" required>
            </td>
            <td>
              <input type="number" name="itens[0][pf]" class="form-control" min="0" step="1">
            </td>
            <td>
              <input type="number" name="itens[0][ust]" class="form-control" min="0" step="1">
            </td>
            <td>
              <input type="number" name="itens[0][horas]" class="form-control" min="0" step="0.01">
            </td>
            <td>
              <input type="text" name="itens[0][valor_total]" class="form-control money-br-input">
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

