@extends('layouts.app')

@section('subtitle', 'Editar Evento')
@section('content_header_title', 'Notificações')
@section('content_header_subtitle', 'Editar evento')

@section('content_body')
<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.notificacoes.update', $evento) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Código</label>
                <input type="text" class="form-control" value="{{ $evento->codigo }}" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $evento->title) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mensagem</label>
                <textarea name="message" class="form-control" rows="3">{{ old('message', $evento->message) }}</textarea>
                <small class="text-muted">Suporta placeholders como {projeto.id}, {usuario_nome}.</small>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prioridade</label>
                    @php($prio = old('priority', $evento->priority ?? 'normal'))
                    <select name="priority" class="form-select">
                        <option value="low" {{ $prio==='low'?'selected':'' }}>Baixa</option>
                        <option value="normal" {{ $prio==='normal'?'selected':'' }}>Normal</option>
                        <option value="high" {{ $prio==='high'?'selected':'' }}>Alta</option>
                        <option value="critical" {{ $prio==='critical'?'selected':'' }}>Crítica</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Escopo de destinatários</label>
                    @php($scope = old('recipient_scope', $evento->recipient_scope ?? 'intersection'))
                    <select name="recipient_scope" class="form-select">
                        <option value="intersection" {{ $scope==='intersection'?'selected':'' }}>RBAC ∩ Contexto</option>
                        <option value="rbac" {{ $scope==='rbac'?'selected':'' }}>Apenas RBAC</option>
                        <option value="context" {{ $scope==='context'?'selected':'' }}>Apenas Contexto</option>
                        <option value="all" {{ $scope==='all'?'selected':'' }}>Todos</option>
                        <option value="roles" {{ $scope==='roles'?'selected':'' }}>Somente papéis específicos</option>
                        <option value="users" {{ $scope==='users'?'selected':'' }}>Somente usuários específicos</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Canais</label>
                @php($chs = old('channels', $evento->channels ?? ['database']))
                <select name="channels[]" class="form-select" multiple>
                    <option value="database" {{ in_array('database', $chs ?? []) ? 'selected' : '' }}>database</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Papéis específicos (se aplicável)</label>
                @php($selRoles = old('recipient_roles', $evento->recipient_roles ?? []))
                <select name="recipient_roles[]" class="form-select" multiple>
                    @isset($roles)
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ in_array($r->id, $selRoles ?? []) ? 'selected' : '' }}>{{ $r->nome ?? $r->name ?? ('Role #'.$r->id) }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Usuários específicos (se aplicável)</label>
                @php($selUsers = old('recipient_users', $evento->recipient_users ?? []))
                <input type="text" id="recipient_users_query" class="form-control mb-2" placeholder="Buscar usuários por nome ou e-mail (mín. 2 letras)">
                <select id="recipient_users" name="recipient_users[]" class="form-select" multiple>
                    @isset($users)
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ in_array($u->id, $selUsers ?? []) ? 'selected' : '' }}>{{ $u->name ?? ('User #'.$u->id) }}</option>
                        @endforeach
                    @endisset
                </select>
                <small class="text-muted">Digite ao menos 2 caracteres para sugerir usuários e adicione ao seletor acima.</small>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="should_generate" id="should_generate" {{ ($evento->should_generate ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="should_generate">Gerar notificações para este evento</label>
            </div>
            <div class="mb-3">
                <label class="form-label">Regras inteligentes (JSON)</label>
                <textarea name="rules" class="form-control" rows="3" placeholder='Ex.: {"threshold":3,"window":"10m"}'>{{ is_array($evento->rules) ? json_encode($evento->rules) : ($evento->rules ?? '') }}</textarea>
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
                        <tbody>
                        @forelse((is_array($evento->workflow) ? $evento->workflow : []) as $idx => $step)
                            <tr>
                                <td><input type="number" name="workflow[{{ $idx }}][step]" class="form-control form-control-sm" value="{{ $step['step'] ?? $idx+1 }}" min="1"></td>
                                <td><input type="text" name="workflow[{{ $idx }}][action]" class="form-control form-control-sm" value="{{ $step['action'] ?? '' }}" placeholder="ex.: medicoes.validar_pf" list="actionsList"></td>
                                <td><input type="text" name="workflow[{{ $idx }}][responsible]" class="form-control form-control-sm" value="{{ $step['responsible'] ?? '' }}" placeholder="ex.: Fiscal Técnico"></td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" name="workflow[{{ $idx }}][notify]" class="form-check-input" {{ !empty($step['notify']) ? 'checked' : '' }}>
                                        <label class="form-check-label">Sim</label>
                                    </div>
                                </td>
                                <td>
                                    <select name="workflow[{{ $idx }}][priority]" class="form-select form-select-sm">
                                        <option value="low" {{ ($step['priority'] ?? 'normal')==='low' ? 'selected' : '' }}>Baixa</option>
                                        <option value="normal" {{ ($step['priority'] ?? 'normal')==='normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="high" {{ ($step['priority'] ?? 'normal')==='high' ? 'selected' : '' }}>Alta</option>
                                        <option value="critical" {{ ($step['priority'] ?? 'normal')==='critical' ? 'selected' : '' }}>Crítica</option>
                                    </select>
                                </td>
                                <td><button type="button" class="btn btn-outline-danger btn-sm remove-step">Remover</button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @isset($actions)
                    <datalist id="actionsList">
                        @foreach($actions as $a)
                            <option value="{{ $a->codigo }}">{{ $a->nome }} ({{ $a->modulo }})</option>
                        @endforeach
                    </datalist>
                    <small class="text-muted">O campo "Ação" sugere valores do catálogo de RBAC.</small>
                @endisset
                <button type="button" class="btn btn-outline-primary btn-sm" id="addWorkflowStep">Adicionar etapa</button>
                <div class="mt-2">
                    <small class="text-muted">Você pode editar etapas diretamente acima. Alternativamente, ajuste o JSON abaixo; se válido, será decodificado.</small>
                </div>
                <textarea name="workflow" class="form-control mt-2" rows="3">{{ !is_array($evento->workflow) ? ($evento->workflow ?? '') : '' }}</textarea>
                <script>
                (function(){
                  const tableBody = document.querySelector('#workflowTable tbody');
                  const addBtn = document.getElementById('addWorkflowStep');
                  function prioSelect(name){
                    return `<select name="${name}" class="form-select form-select-sm">`
                      + `<option value=\"low\">Baixa</option>`
                      + `<option value=\"normal\" selected>Normal</option>`
                      + `<option value=\"high\">Alta</option>`
                      + `<option value=\"critical\">Crítica</option>`
                      + `</select>`;
                  }
                  function addRow(step = '', action = '', responsible = '', notify = true, priority = 'normal'){
                    const idx = tableBody.children.length;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                      <td><input type="number" name="workflow[${idx}][step]" class="form-control form-control-sm" value="${step}" min="1"></td>
                      <td><input type="text" name="workflow[${idx}][action]" class="form-control form-control-sm" placeholder="ex.: medicoes.homologar" value="${action}" list="actionsList"></td>
                      <td><input type="text" name="workflow[${idx}][responsible]" class="form-control form-control-sm" placeholder="ex.: Gestor" value="${responsible}"></td>
                      <td>
                        <div class="form-check">
                          <input type="checkbox" name="workflow[${idx}][notify]" class="form-check-input" ${notify ? 'checked' : ''}>
                          <label class="form-check-label">Sim</label>
                        </div>
                      </td>
                      <td>${prioSelect(`workflow[${idx}][priority]`).replace(`value=\\\"${priority}\\\"`, `value=\\\"${priority}\\\" selected`)}</td>
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
                        Array.from(tableBody.children).forEach(function(tr, i){
                          tr.querySelectorAll('input, select').forEach(function(el){
                            el.name = el.name.replace(/workflow\[\d+\]/, `workflow[${i}]`);
                          });
                        });
                      }
                    });
                  }
                })();
                </script>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="enabled" id="enabled" {{ $evento->enabled ? 'checked' : '' }}>
                <label class="form-check-label" for="enabled">Ativo</label>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('admin.notificacoes.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
</div>
</div>
@endsection
@section('js')
<script>
(function(){
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
})();
</script>
@endsection