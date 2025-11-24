@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Evento de Notificação</h5>
                    <div>
                        <a href="{{ route('admin.notificacoes.edit', $evento) }}" class="btn btn-sm btn-primary">Editar</a>
                        <a href="{{ route('admin.notificacoes.index') }}" class="btn btn-sm btn-secondary">Voltar</a>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-3">
                        <dt class="col-sm-3">Código</dt>
                        <dd class="col-sm-9">{{ $evento->codigo }}</dd>

                        <dt class="col-sm-3">Título</dt>
                        <dd class="col-sm-9">{{ $evento->title }}</dd>

                        <dt class="col-sm-3">Domínio</dt>
                        <dd class="col-sm-9">{{ $evento->dominio ?? '-' }}</dd>

                        <dt class="col-sm-3">Prioridade</dt>
                        <dd class="col-sm-9">{{ $evento->priority ?? 'normal' }}</dd>

                        <dt class="col-sm-3">Canais</dt>
                        <dd class="col-sm-9">{{ is_array($evento->channels) ? implode(', ', $evento->channels) : ($evento->channels ?? '-') }}</dd>

                        <dt class="col-sm-3">Habilitado</dt>
                        <dd class="col-sm-9">{{ $evento->enabled ? 'Sim' : 'Não' }}</dd>
                    </dl>

                    <h6>Workflow</h6>
                    @if(is_array($evento->workflow) && count($evento->workflow))
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:70px">Etapa</th>
                                        <th>Ação</th>
                                        <th>Responsável</th>
                                        <th style="width:120px">Notificação?</th>
                                        <th style="width:130px">Prioridade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evento->workflow as $idx => $step)
                                        <tr>
                                            <td>{{ $step['step'] ?? ($idx+1) }}</td>
                                            <td><code>{{ $step['action'] ?? '' }}</code></td>
                                            <td>{{ $step['responsible'] ?? '-' }}</td>
                                            <td>{{ !empty($step['notify']) ? 'Sim' : 'Não' }}</td>
                                            <td>{{ $step['priority'] ?? 'normal' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Sem etapas definidas ou workflow armazenado como texto.</p>
                        @if(is_string($evento->workflow) && trim($evento->workflow) !== '')
                            <pre class="bg-light p-2 border">{{ $evento->workflow }}</pre>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection