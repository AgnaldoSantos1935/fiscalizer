@extends('layouts.app')

@section('subtitle', 'Cadastrar Evento')
@section('content_header_title', 'Notificações')
@section('content_header_subtitle', 'Cadastrar evento')

@section('content_body')
<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.notificacoes.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Código</label>
                <input type="text" name="codigo" class="form-control" placeholder="notificacoes.dominio.evento" required>
                <small class="text-muted">Ex.: notificacoes.projetos.projeto_criado</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="title" class="form-control" placeholder="Título do evento" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mensagem</label>
                <textarea name="message" class="form-control" rows="3" placeholder="Mensagem opcional com placeholders"></textarea>
                <small class="text-muted">Suporta placeholders como {projeto.id}, {usuario_nome}.</small>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prioridade</label>
                    <select name="priority" class="form-select">
                        <option value="low">Baixa</option>
                        <option value="normal" selected>Normal</option>
                        <option value="high">Alta</option>
                        <option value="critical">Crítica</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Escopo de destinatários</label>
                    <select name="recipient_scope" class="form-select">
                        <option value="intersection" selected>RBAC ∩ Contexto</option>
                        <option value="rbac">Apenas RBAC</option>
                        <option value="context">Apenas Contexto</option>
                        <option value="all">Todos</option>
                        <option value="roles">Somente papéis específicos</option>
                        <option value="users">Somente usuários específicos</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Canais</label>
                <select name="channels[]" class="form-select" multiple>
                    <option value="database" selected>database</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Papéis específicos (se aplicável)</label>
                <select name="recipient_roles[]" class="form-select" multiple>
                    @isset($roles)
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->nome ?? $r->name ?? ('Role #'.$r->id) }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Usuários específicos (se aplicável)</label>
                <input type="text" id="recipient_users_query" class="form-control mb-2" placeholder="Buscar usuários por nome ou e-mail (mín. 2 letras)">
                <select id="recipient_users" name="recipient_users[]" class="form-select" multiple>
                    @isset($users)
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name ?? ('User #'.$u->id) }}</option>
                        @endforeach
                    @endisset
                </select>
                <small class="text-muted">Digite ao menos 2 caracteres para sugerir usuários e adicione ao seletor acima.</small>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="should_generate" id="should_generate" checked>
                <label class="form-check-label" for="should_generate">Gerar notificações para este evento</label>
            </div>
            <div class="mb-3">
                <label class="form-label">Regras inteligentes (JSON)</label>
                <textarea name="rules" class="form-control" rows="3" placeholder='Ex.: {"threshold":3,"window":"10m"}'></textarea>
                <small class="text-muted">Aceita objeto/array JSON. Ex.: {"threshold":3}. Texto simples também é aceito e será salvo como string.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Workflow</label>
                <div class="table-responsive mb-2">
                    <table class="table table-sm align-middle" id="workflowTable">
                        <thead>
                            <tr>
                                <th style="width:70px">Etapa</th>
                                <th>Ação</th>
                                <th>Responsável</th>
                                <th style="width:120px">Notificação?</th>
                                <th style="width:130px">Prioridade</th>
                                <th style="width:80px"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addWorkflowStep">Adicionar etapa</button>
                <div class="mt-2">
                    <small class="text-muted">Você pode editar etapas diretamente acima. Alternativamente, cole JSON abaixo; se válido, será decodificado.</small>
                </div>
                <textarea name="workflow" class="form-control mt-2" rows="3" placeholder='Opcional: cole JSON do workflow aqui'></textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="enabled" id="enabled" checked>
                <label class="form-check-label" for="enabled">Ativo</label>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar Evento</button>
            <a href="{{ route('admin.notificacoes.index') }}" class="btn btn-secondary">Cancelar</a>
            @isset($actions)
            <datalist id="actionsList">
                @foreach($actions as $a)
                    <option value="{{ $a->codigo }}">{{ $a->nome ?? $a->codigo }} ({{ $a->modulo }})</option>
                @endforeach
            </datalist>
            @endisset
        </form>
    </div>
    </div>
</div>
@endsection
@section('js')
<script>
(function(){
  // ---- Workflow Editor (Create) ----
  const tableBody = document.querySelector('#workflowTable tbody');
  const addBtn = document.getElementById('addWorkflowStep');
  function prioSelect(name){
    return `<select name="${name}" class="form-select form-select-sm">
              <option value="low">Baixa</option>
              <option value="normal" selected>Normal</option>
              <option value="high">Alta</option>
              <option value="critical">Crítica</option>
            </select>`;
  }
  function addRow(step = '', action = '', responsible = '', notify = true, priority = 'normal'){
    const idx = tableBody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input type="number" name="workflow[${idx}][step]" class="form-control form-control-sm" value="${step}" min="1"></td>
      <td><input type="text" name="workflow[${idx}][action]" list="actionsList" class="form-control form-control-sm" placeholder="ex.: medicoes.validar_pf" value="${action}"></td>
      <td><input type="text" name="workflow[${idx}][responsible]" class="form-control form-control-sm" placeholder="ex.: Fiscal Técnico" value="${responsible}"></td>
      <td>
        <div class="form-check">
          <input type="checkbox" name="workflow[${idx}][notify]" class="form-check-input" ${notify ? 'checked' : ''}>
          <label class="form-check-label">Sim</label>
        </div>
      </td>
      <td>${prioSelect(`workflow[${idx}][priority]`).replace(`value="${priority}"`, `value="${priority}" selected`)}</td>
      <td><button type="button" class="btn btn-outline-danger btn-sm remove-step">Remover</button></td>`;
    tableBody.appendChild(tr);
  }
  if (addBtn) {
    addBtn.addEventListener('click', function(){ addRow(tableBody.children.length + 1, '', '', true, 'normal'); });
  }
  if (tableBody) {
    tableBody.addEventListener('click', function(ev){
      if (ev.target && ev.target.classList.contains('remove-step')) {
        const row = ev.target.closest('tr');
        row && row.remove();
        // Reindexa nomes
        Array.from(tableBody.children).forEach(function(tr, i){
          tr.querySelectorAll('input, select').forEach(function(el){
            el.name = el.name.replace(/workflow\[\d+\]/, `workflow[${i}]`);
          });
        });
      }
    });
  }

  const $query = document.getElementById('recipient_users_query');
  const $select = document.getElementById('recipient_users');
  let timer;
  function fetchUsers(q){
    if(!q || q.length < 2) return;
    fetch('{{ route('admin.notificacoes.users.search') }}?q=' + encodeURIComponent(q))
      .then(r => r.json())
      .then(list => {
        list.forEach(u => {
          const exists = Array.from($select.options).some(o => String(o.value) === String(u.id));
          if(!exists){
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.textContent = u.text || u.name || ('User #' + u.id);
            $select.appendChild(opt);
          }
        });
      })
      .catch(() => {});
  }
  if($query && $select){
    $query.addEventListener('input', function(){
      clearTimeout(timer);
      const val = this.value.trim();
      timer = setTimeout(() => fetchUsers(val), 300);
    });
  }
  if (tableBody && tableBody.children.length === 0) {
    const presets = [
      { action: 'contratos.elaboracao', responsible: 'Compras' },
      { action: 'contratos.gestao', responsible: 'Gestor do Contrato' },
      { action: 'contratos.fiscalizacao.tecnica', responsible: 'Fiscal Técnico' },
      { action: 'contratos.fiscalizacao.administrativa', responsible: 'Administrador do Contrato' },
      { action: 'medicoes.registro', responsible: 'Executor' },
      { action: 'empenhos.registro', responsible: 'Orçamento/Contabilidade' },
      { action: 'pagamentos.processamento', responsible: 'Financeiro' },
    ];
    presets.forEach((p, i) => addRow(i + 1, p.action, p.responsible, true, 'normal'));
  }
})();
</script>
@endsection
