@extends('layouts.app')
@section('title', 'Detalhes do Termo de Referência')

@section('content_body')
<div class="container-fluid">
    @php($totalItens = $tr->itens->sum('valor_total'))
    @php($diff = ($tr->valor_estimado ?? 0) - $totalItens)
    @php($labels = [
        'enviar_aprovacao' => 'Enviado para aprovação',
        'aprovar' => 'Aprovado',
        'retornar' => 'Retornado para elaboração',
        'reprovar' => 'Reprovado',
    ])
    @php($badges = [
        'enviar_aprovacao' => 'secondary',
        'aprovar' => 'success',
        'retornar' => 'warning',
        'reprovar' => 'danger',
    ])
    @php($logs = $tr->logs ?? collect())
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold"><i class="fas fa-file-alt text-primary me-2"></i>{{ $tr->titulo }}</h4>
            @php($podeAprovar = auth()->user()?->hasRole(['Administrador','Gestor de Contrato']))
            <div class="d-flex gap-2">
                @if($tr->status === 'rascunho')
                    <form action="{{ route('contratacoes.termos-referencia.enviar-aprovacao', $tr) }}" method="POST" onsubmit="return confirm('Enviar para aprovação?');">
                        @csrf
                        <button class="btn btn-success btn-sm"><i class="fas fa-paper-plane me-1"></i>Enviar para aprovação</button>
                    </form>
                @elseif($tr->status === 'em_analise' && $podeAprovar)
                    <form action="{{ route('contratacoes.termos-referencia.aprovar', $tr) }}" method="POST" onsubmit="return confirm('Aprovar este Termo de Referência?');">
                        @csrf
                        <button class="btn btn-primary btn-sm"><i class="fas fa-check me-1"></i>Aprovar</button>
                    </form>
                    <form action="{{ route('contratacoes.termos-referencia.retornar-elaboracao', $tr) }}" method="POST" onsubmit="return confirm('Retornar para elaboração?');">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm"><i class="fas fa-undo me-1"></i>Retornar para elaboração</button>
                    </form>
                    <button class="btn btn-outline-warning btn-sm" type="button" onclick="document.getElementById('reprovar-form').classList.toggle('d-none')">
                        <i class="fas fa-times-circle me-1"></i>Reprovar
                    </button>
                @endif
                @if($tr->status !== 'finalizado')
                    <a href="{{ route('contratacoes.termos-referencia.edit', $tr) }}" class="btn btn-outline-warning btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
                @endif
                <a href="{{ route('contratacoes.termos-referencia.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
            </div>
        </div>
        <div class="card-body bg-white content-scroll">
            @if($tr->status === 'em_analise' && $podeAprovar)
            <form id="reprovar-form" action="{{ route('contratacoes.termos-referencia.reprovar', $tr) }}" method="POST" class="alert alert-warning d-none">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Motivo da reprovação</label>
                    <textarea name="motivo" class="form-control" rows="2" required placeholder="Descreva o motivo"></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-warning btn-sm" onclick="return confirm('Confirmar reprovação e retorno para elaboração?');"><i class="fas fa-times-circle me-1"></i>Confirmar Reprovação</button>
                </div>
            </form>
            @endif
            <dl class="row">
                <dt class="col-md-3">Status</dt>
                <dd class="col-md-9"><span class="badge bg-secondary">{{ $tr->status }}</span></dd>

                <dt class="col-md-3">Valor Estimado</dt>
                <dd class="col-md-9">{{ $tr->valor_estimado ? number_format($tr->valor_estimado, 2, ',', '.') : '—' }}</dd>

                <dt class="col-md-3">Objeto</dt>
                <dd class="col-md-9">{!! nl2br(e($tr->objeto)) !!}</dd>

                <dt class="col-md-3">Justificativa</dt>
                <dd class="col-md-9">{!! nl2br(e($tr->justificativa)) !!}</dd>

                <dt class="col-md-3">Escopo</dt>
                <dd class="col-md-9">{!! nl2br(e($tr->escopo)) !!}</dd>

                <dt class="col-md-3">Requisitos</dt>
                <dd class="col-md-9">{!! nl2br(e($tr->requisitos)) !!}</dd>

                <dt class="col-md-3">Critérios de Julgamento</dt>
                <dd class="col-md-9">{!! nl2br(e($tr->criterios_julgamento)) !!}</dd>

                <dt class="col-md-3">Prazos</dt>
                <dd class="col-md-9">{!! nl2br(e($tr->prazos)) !!}</dd>

                <dt class="col-md-3">Local de Execução</dt>
                <dd class="col-md-9">{!! nl2br(e($tr->local_execucao)) !!}</dd>

                <dt class="col-md-3">Forma de Pagamento</dt>
                <dd class="col-md-9">{!! nl2br(e($tr->forma_pagamento)) !!}</dd>
            </dl>

            <hr class="my-4" />

            <h5 class="text-secondary fw-semibold mb-3"><i class="fas fa-list-ul text-primary me-2"></i>Itens do Termo de Referência</h5>

            <div class="table-responsive mb-3">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descrição</th>
                            <th>Unidade</th>
                            <th class="text-end">Quantidade</th>
                            <th class="text-end">Valor Unitário</th>
                            <th class="text-end">Valor Total</th>
                            <th class="text-center" style="width: 90px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tr->itens as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ $item->descricao }}</span>
                                        @if($tr->status !== 'finalizado')
                                        <button type="button" class="btn btn-outline-secondary btn-xs" onclick="toggleEdit({{ $item->id }})"><i class="fas fa-edit"></i></button>
                                        @endif
                                    </div>
                                    <form id="edit-form-{{ $item->id }}" action="{{ route('contratacoes.termos-referencia.itens.update', $item) }}" method="POST" class="mt-2 d-none">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <input type="text" name="descricao" class="form-control form-control-sm" value="{{ $item->descricao }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" name="unidade" class="form-control form-control-sm" value="{{ $item->unidade }}" placeholder="un, kg">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" name="quantidade" class="form-control form-control-sm input-number" value="{{ $item->quantidade }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">R$</span>
                                                    <input type="text" name="valor_unitario" class="form-control form-control-sm input-money" value="{{ $item->valor_unitario }}" required>
                                                </div>
                                            </div>
                                            <div class="col-12 d-flex justify-content-end">
                                                <button class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Salvar</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>{{ $item->unidade ?? '—' }}</td>
                                <td class="text-end">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($tr->status !== 'finalizado')
                                    <form action="{{ route('contratacoes.termos-referencia.itens.destroy', $item) }}" method="POST" onsubmit="return confirm('Remover este item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted">Nenhum item cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total dos Itens</th>
                            <th class="text-end">R$ {{ number_format($totalItens, 2, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($tr->status !== 'finalizado')
            <form action="{{ route('contratacoes.termos-referencia.itens.store', $tr) }}" method="POST" class="card border-0 shadow-sm rounded-3">
                @csrf
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-secondary fw-semibold"><i class="fas fa-plus-circle text-success me-2"></i>Adicionar Item</h6>
                </div>
                <div class="card-body bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Descrição</label>
                            <input type="text" name="descricao" class="form-control" required />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Unidade</label>
                            <input type="text" name="unidade" class="form-control" placeholder="ex: un, kg" />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantidade</label>
                            <input type="text" name="quantidade" class="form-control input-number" required />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Valor Unitário (R$)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="valor_unitario" class="form-control input-money" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-end">
                    <button class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Inserir Item</button>
                </div>
            </form>
            @endif

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-semibold text-secondary mb-3">Resumo de Totais</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between"><span>Total dos Itens</span><span>R$ {{ number_format($totalItens, 2, ',', '.') }}</span></li>
                                <li class="d-flex justify-content-between"><span>Valor Estimado (cabeçalho)</span><span>R$ {{ number_format($tr->valor_estimado ?? 0, 2, ',', '.') }}</span></li>
                                <li class="d-flex justify-content-between"><span>Diferença (Estimado − Itens)</span><span class="{{ $diff >= 0 ? 'text-success' : 'text-danger' }}">R$ {{ number_format($diff, 2, ',', '.') }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-semibold text-secondary mb-3">Histórico de Workflow</h6>
                            @if($logs->count() === 0)
                                <div class="text-muted">Nenhum registro de workflow.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Data</th>
                                                <th>Usuário</th>
                                                <th>Ação</th>
                                                <th>Motivo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($logs as $log)
                                                <tr>
                                                    <td>{{ optional($log->created_at)->format('d/m/Y H:i') }}</td>
                                                    <td>{{ optional($log->usuario)->name ?? '—' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $badges[$log->acao] ?? 'secondary' }}">{{ $labels[$log->acao] ?? $log->acao }}</span>
                                                    </td>
                                                    <td class="text-muted">{{ $log->motivo ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleEdit(id) {
    const el = document.getElementById('edit-form-' + id);
    if (!el) return;
    el.classList.toggle('d-none');
}

function parseNumberBR(value) {
    if (!value) return 0;
    // Remove anything that's not digit or comma/dot, then normalize
    let v = ('' + value).replace(/[^0-9,\.]/g, '');
    // If has both comma and dot, assume dot as thousands and comma as decimal
    if (v.indexOf(',') !== -1 && v.indexOf('.') !== -1) {
        v = v.replace(/\./g, '').replace(',', '.');
    } else if (v.indexOf(',') !== -1) {
        v = v.replace(',', '.');
    }
    const n = parseFloat(v);
    return isNaN(n) ? 0 : n;
}

function formatMoneyBR(n) {
    return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

document.addEventListener('DOMContentLoaded', function() {
    // Money mask
    document.querySelectorAll('.input-money').forEach(function(input) {
        input.addEventListener('blur', function(e) {
            const n = parseNumberBR(e.target.value);
            e.target.value = formatMoneyBR(n);
        });
    });
    // Number mask
    document.querySelectorAll('.input-number').forEach(function(input) {
        input.addEventListener('blur', function(e) {
            const n = parseNumberBR(e.target.value);
            e.target.value = n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
    });
});
</script>
@endpush
