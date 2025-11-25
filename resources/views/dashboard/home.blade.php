@extends('layouts.app')

@section('title', 'Dashboard - Fiscalizer')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

  @php
    use Illuminate\Support\Facades\Gate;
    $canContratos = Gate::allows('view-contratos');
    $canProjetos = Gate::allows('view-projetos');
    $canMedicoes = Gate::allows('view-medicoes');
    $canMapas = Gate::allows('view-escolas');
    $canMonitoramentos = Gate::allows('view-index-monitoramento');
    $canUserProfiles = Gate::allows('view-index-user_profiles');
  @endphp

  <!-- 🔹 Apresentação e Avisos -->
  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="card ui-card shadow-sm border-0 rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-home text-primary fa-lg me-2"></i>
            <h5 class="mb-0">Bem-vindo{{ isset($usuario) && $usuario ? ", ".$usuario->name : '' }}!</h5>
          </div>
          <p class="text-muted mb-3">Esta é a sua página inicial. Aqui você encontra atalhos para as funcionalidades principais e um resumo de avisos para sua conta.</p>

          <div class="row g-3">
            <div class="col-sm-6 col-md-4">
              @if($canContratos)
                <a href="{{ route('contratos.index') }}" class="text-decoration-none">
                  <div class="ui-card p-3 h-100 hover-shadow enabled">
                    <div class="d-flex align-items-center mb-2"><i class="fas fa-file-contract text-primary me-2"></i><strong>Contratos</strong></div>
                    <small class="text-muted">Cadastro, gestão e conformidade de contratos.</small>
                  </div>
                </a>
              @else
                <div class="ui-card p-3 h-100 hover-shadow disabled">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-file-contract text-secondary me-2"></i><strong>Contratos</strong></div>
                  <small class="text-muted">Recurso indisponível para seu perfil.</small>
                </div>
              @endif
            </div>

            <div class="col-sm-6 col-md-4">
              @if($canProjetos)
                <a href="{{ route('projetos.index') }}" class="text-decoration-none">
                  <div class="ui-card p-3 h-100 hover-shadow enabled">
                    <div class="d-flex align-items-center mb-2"><i class="fas fa-diagram-project text-success me-2"></i><strong>Projetos</strong></div>
                    <small class="text-muted">Portfólio, produtividade e indicadores.</small>
                  </div>
                </a>
              @else
                <div class="ui-card p-3 h-100 hover-shadow disabled">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-diagram-project text-secondary me-2"></i><strong>Projetos</strong></div>
                  <small class="text-muted">Recurso indisponível para seu perfil.</small>
                </div>
              @endif
            </div>

            <div class="col-sm-6 col-md-4">
              @if($canMedicoes)
                <a href="{{ route('medicoes.index') }}" class="text-decoration-none">
                  <div class="ui-card p-3 h-100 hover-shadow enabled">
                    <div class="d-flex align-items-center mb-2"><i class="fas fa-calculator text-info me-2"></i><strong>Medições</strong></div>
                    <small class="text-muted">Ciclos de medição e boletins.</small>
                  </div>
                </a>
              @else
                <div class="ui-card p-3 h-100 hover-shadow disabled">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-calculator text-secondary me-2"></i><strong>Medições</strong></div>
                  <small class="text-muted">Recurso indisponível para seu perfil.</small>
                </div>
              @endif
            </div>

            <div class="col-sm-6 col-md-4">
              @if($canMapas)
                <a href="{{ route('mapas.escolas') }}" class="text-decoration-none">
                  <div class="ui-card p-3 h-100 hover-shadow enabled">
                    <div class="d-flex align-items-center mb-2"><i class="fas fa-map-marked-alt text-warning me-2"></i><strong>Mapas</strong></div>
                    <small class="text-muted">Exploração geográfica das escolas e filtros.</small>
                  </div>
                </a>
              @else
                <div class="ui-card p-3 h-100 hover-shadow disabled">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-map-marked-alt text-secondary me-2"></i><strong>Mapas</strong></div>
                  <small class="text-muted">Recurso indisponível para seu perfil.</small>
                </div>
              @endif
            </div>

            <div class="col-sm-6 col-md-4">
              <a href="{{ route('user_profiles.me') }}" class="text-decoration-none">
                <div class="ui-card p-3 h-100 hover-shadow enabled">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-id-badge text-secondary me-2"></i><strong>Meu Perfil</strong></div>
                  <small class="text-muted">Acesse e atualize seu próprio perfil.</small>
                </div>
              </a>
            </div>

            <div class="col-sm-6 col-md-4">
              @if($canMonitoramentos)
                <a href="{{ route('monitoramentos.index') }}" class="text-decoration-none">
                  <div class="ui-card p-3 h-100 hover-shadow enabled">
                    <div class="d-flex align-items-center mb-2"><i class="fas fa-server text-secondary me-2"></i><strong>Monitoramentos</strong></div>
                    <small class="text-muted">Saúde dos serviços e hosts monitorados.</small>
                  </div>
                </a>
              @else
                <div class="ui-card p-3 h-100 hover-shadow disabled">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-server text-secondary me-2"></i><strong>Monitoramentos</strong></div>
                  <small class="text-muted">Recurso indisponível para seu perfil.</small>
                </div>
              @endif
            </div>
            <div class="col-sm-6 col-md-4">
              @if($canUserProfiles)
                <a href="{{ route('user_profiles.index') }}" class="text-decoration-none">
                  <div class="ui-card p-3 h-100 hover-shadow enabled">
                    <div class="d-flex align-items-center mb-2"><i class="fas fa-users text-primary me-2"></i><strong>Lista de Usuários</strong></div>
                    <small class="text-muted">Gerencie perfis e usuários do sistema.</small>
                  </div>
                </a>
              @else
                <div class="ui-card p-3 h-100 hover-shadow disabled">
                  <div class="d-flex align-items-center mb-2"><i class="fas fa-users text-secondary me-2"></i><strong>Lista de Usuários</strong></div>
                  <small class="text-muted">Recurso indisponível para seu perfil.</small>
                </div>
              @endif
            </div>
            <div class="col-sm-6 col-md-4">
              <a href="{{ route('notificacoes.index') }}" class="text-decoration-none">
                <div class="ui-card p-3 h-100 hover-shadow">
                  <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-bell text-danger me-2"></i><strong>Notificações</strong>
                    @if(($notificacoesNaoLidas ?? 0) > 0)
                      <span class="badge bg-danger ms-2">{{ $notificacoesNaoLidas }}</span>
                    @endif
                  </div>
                  <small class="text-muted">Avisos e alertas da sua conta.</small>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card ui-card shadow-sm border-0 rounded-4 h-100">
        <div class="card-header ui-card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0 text-secondary fw-semibold"><i class="fas fa-bell text-danger me-1"></i>Avisos</h6>
          <a href="{{ route('notificacoes.index') }}" class="btn btn-sm ui-btn outline">Ver todas</a>
        </div>
        <div class="card-body">
          @forelse(($ultimasNotificacoes ?? collect()) as $n)
            <div class="mb-3">
              <div class="d-flex align-items-center">
                @if(!$n->lida)
                  <span class="badge bg-danger me-2">Nova</span>
                @else
                  <span class="badge bg-secondary me-2">Lida</span>
                @endif
                <strong>{{ $n->titulo }}</strong>
              </div>
              <div class="small text-muted">{{ $n->mensagem }}</div>
              <div class="d-flex justify-content-between mt-1">
                <div class="small text-muted">{{ optional($n->created_at)->format('d/m/Y H:i') }}</div>
                @if($n->link)
                  <a href="{{ $n->link }}" class="small">Abrir</a>
                @endif
              </div>
            </div>
          @empty
            <div class="text-muted">Sem avisos recentes.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-12">
      <h3 class="fw-semibold text-secondary">
        <i class="fas fa-tachometer-alt text-primary me-2"></i>Painel Geral do Sistema Fiscalizer
      </h3>
      <p class="text-muted mb-0">Visão consolidada de contratos, projetos e medições</p>
    </div>
  </div>

  <!-- 🔹 Indicadores principais -->
  <div class="row g-3 mb-4">
    <div class="col-md-2">
      <div class="card metric {{ $canContratos ? 'enabled' : 'disabled' }} text-center shadow-sm rounded-4" @if($canContratos) role="button" data-bs-toggle="modal" data-bs-target="#modalContratos" @endif>
        <div class="card-body">
          <i class="fas fa-file-contract fa-2x mb-2 text-primary"></i>
          <h4>{{ $canContratos ? $totalContratos : 0 }}</h4>
          <small class="text-primary fw-semibold">Contratos</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card metric {{ $canProjetos ? 'enabled' : 'disabled' }} text-center shadow-sm rounded-4" @if($canProjetos) role="button" data-bs-toggle="modal" data-bs-target="#modalProjetos" @endif>
        <div class="card-body">
          <i class="fas fa-diagram-project fa-2x mb-2 text-success"></i>
          <h4>{{ $canProjetos ? $totalProjetos : 0 }}</h4>
          <small class="text-success fw-semibold">Projetos</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card metric {{ $canMedicoes ? 'enabled' : 'disabled' }} text-center shadow-sm rounded-4" @if($canMedicoes) role="button" data-bs-toggle="modal" data-bs-target="#modalMedicoes" @endif>
        <div class="card-body">
          <i class="fas fa-calculator fa-2x mb-2 text-info"></i>
          <h4>{{ $canMedicoes ? $totalMedicoes : 0 }}</h4>
          <small class="text-info fw-semibold">Medições</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card metric {{ $canMedicoes ? 'enabled' : 'disabled' }} text-center shadow-sm rounded-4">
        <div class="card-body">
          <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
          <h4>{{ $canMedicoes ? $totalBoletins : 0 }}</h4>
          <small>Boletins</small>
        </div>
      </div>
</div>

  <!-- 🔹 Modais de detalhes -->
  @if($canContratos)
  <div class="modal fade" id="modalContratos" tabindex="-1" aria-labelledby="modalContratosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalContratosLabel"><i class="fas fa-file-contract text-primary me-2"></i>Contratos do Usuário</h5>
          <small id="contratosCount" class="text-secondary"></small>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          @php
            $contratosSituacoes = ($contratosResumo ?? collect())->pluck('situacao')->filter()->unique()->values();
            $contratosTipos = ($contratosResumo ?? collect())->pluck('tipo')->filter()->unique()->values();
          @endphp
          <div class="px-3 pt-3 pb-2 border-bottom bg-light">
            <div class="row g-2 align-items-end">
              <div class="col-md-4">
                <label class="form-label small text-secondary">Buscar por número</label>
                <input id="filtroContratoTexto" type="text" class="form-control" placeholder="Ex.: 123/2024">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-secondary">Situação</label>
                <select id="filtroContratoSituacao" class="form-select">
                  <option value="">Todas</option>
                  @foreach($contratosSituacoes as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small text-secondary">Tipo</label>
                <select id="filtroContratoTipo" class="form-select">
                  <option value="">Todos</option>
                  @foreach($contratosTipos as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <table class="table table-hover dt-skip mb-0">
            <thead class="table-light">
              <tr>
                <th class="sortable" data-sort="id" data-type="number">ID <span class="sort-ind"></span></th>
                <th class="sortable" data-sort="numero" data-type="string">Número <span class="sort-ind"></span></th>
                <th class="sortable" data-sort="situacao" data-type="string">Situação <span class="sort-ind"></span></th>
                <th class="sortable" data-sort="tipo" data-type="string">Tipo <span class="sort-ind"></span></th>
              </tr>
            </thead>
            <tbody>
              @forelse(($contratosResumo ?? collect()) as $c)
                <tr data-id="{{ $c->id }}" data-numero="{{ Str::lower($c->numero ?? '') }}" data-situacao="{{ $c->situacao }}" data-tipo="{{ $c->tipo }}">
                  <td>{{ $c->id }}</td>
                  <td><a href="{{ route('contratos.show', $c->id) }}" class="text-decoration-none">{{ $c->numero }}</a></td>
                  <td>{{ $c->situacao ?? '—' }}</td>
                  <td>{{ $c->tipo ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Nenhum contrato encontrado.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <a href="{{ route('contratos.index') }}" class="btn btn-primary"><i class="fas fa-list me-1"></i> Ver todos</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
  @endif

  @if($canProjetos)
  <div class="modal fade" id="modalProjetos" tabindex="-1" aria-labelledby="modalProjetosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalProjetosLabel"><i class="fas fa-diagram-project text-success me-2"></i>Projetos dos Contratos</h5>
          <small id="projetosCount" class="text-secondary"></small>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          @php
            $projetosSituacoes = ($projetosResumo ?? collect())->pluck('situacao')->filter()->unique()->values();
            $projetosStatuses = ($projetosResumo ?? collect())->pluck('status')->filter()->unique()->values();
          @endphp
          <div class="px-3 pt-3 pb-2 border-bottom bg-light">
            <div class="row g-2 align-items-end">
              <div class="col-md-4">
                <label class="form-label small text-secondary">Buscar por código/título</label>
                <input id="filtroProjetoTexto" type="text" class="form-control" placeholder="Ex.: SIS-001 ou Portal">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-secondary">Situação</label>
                <select id="filtroProjetoSituacao" class="form-select">
                  <option value="">Todas</option>
                  @foreach($projetosSituacoes as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small text-secondary">Status</label>
                <select id="filtroProjetoStatus" class="form-select">
                  <option value="">Todos</option>
                  @foreach($projetosStatuses as $st)
                    <option value="{{ $st }}">{{ $st }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <table class="table table-hover dt-skip mb-0">
            <thead class="table-light">
              <tr>
                <th class="sortable" data-sort="id" data-type="number">ID <span class="sort-ind"></span></th>
                <th class="sortable" data-sort="codigo" data-type="string">Código <span class="sort-ind"></span></th>
                <th class="sortable" data-sort="titulo" data-type="string">Título <span class="sort-ind"></span></th>
                <th class="sortable" data-sort="situacao" data-type="string">Situação <span class="sort-ind"></span></th>
                <th class="sortable" data-sort="status" data-type="string">Status <span class="sort-ind"></span></th>
              </tr>
            </thead>
            <tbody>
              @forelse(($projetosResumo ?? collect()) as $p)
                <tr data-id="{{ $p->id }}" data-codigo="{{ Str::lower($p->codigo ?? '') }}" data-titulo="{{ Str::lower($p->titulo ?? '') }}" data-situacao="{{ $p->situacao }}" data-status="{{ $p->status }}">
                  <td>{{ $p->id }}</td>
                  <td>{{ $p->codigo ?? '—' }}</td>
                  <td><a href="{{ route('projetos.show', $p->id) }}" class="text-decoration-none">{{ $p->titulo }}</a></td>
                  <td>{{ $p->situacao ?? '—' }}</td>
                  <td>{{ $p->status ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Nenhum projeto encontrado.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <a href="{{ route('projetos.index') }}" class="btn btn-success"><i class="fas fa-list me-1"></i> Ver todos</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
  @endif

  @if($canMedicoes)
  <div class="modal fade" id="modalMedicoes" tabindex="-1" aria-labelledby="modalMedicoesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalMedicoesLabel"><i class="fas fa-calculator text-info me-2"></i>Medições dos Contratos</h5>
          <small id="medicoesCount" class="text-secondary"></small>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          @php
            $medicoesStatuses = ($medicoesResumo ?? collect())->pluck('status')->filter()->unique()->values();
          @endphp
          <div class="px-3 pt-3 pb-2 border-bottom bg-light">
            <div class="row g-2 align-items-end">
              <div class="col-md-6">
                <label class="form-label small text-secondary">Buscar por competência</label>
                <input id="filtroMedicaoTexto" type="text" class="form-control" placeholder="Ex.: 2025-10 ou 2025-10-01">
              </div>
              <div class="col-md-6">
                <label class="form-label small text-secondary">Status</label>
                <select id="filtroMedicaoStatus" class="form-select">
                  <option value="">Todos</option>
                  @foreach($medicoesStatuses as $st)
                    <option value="{{ $st }}">{{ $st }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <table class="table table-hover dt-skip mb-0">
            <thead class="table-light">
              <tr>
                <th class="sortable" data-sort="id" data-type="number">ID <span class="sort-ind"></span></th>
                <th class="sortable" data-sort="competencia" data-type="string">Competência <span class="sort-ind"></span></th>
                <th class="sortable" data-sort="status" data-type="string">Status <span class="sort-ind"></span></th>
                <th class="text-end sortable" data-sort="valor" data-type="number">Valor (R$) <span class="sort-ind"></span></th>
              </tr>
            </thead>
            <tbody>
              @forelse(($medicoesResumo ?? collect()) as $m)
                <tr data-id="{{ $m->id }}" data-competencia="{{ Str::lower($m->competencia ?? '') }}" data-status="{{ $m->status }}" data-valor="{{ $m->valor_liquido }}">
                  <td>{{ $m->id }}</td>
                  <td>{{ $m->competencia ?? '—' }}</td>
                  <td>{{ $m->status ?? '—' }}</td>
                  <td class="text-end">{{ isset($m->valor_liquido) ? ('R$ '.number_format($m->valor_liquido, 2, ',', '.')) : '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Nenhuma medição encontrada.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <a href="{{ route('medicoes.index') }}" class="btn btn-info text-white"><i class="fas fa-list me-1"></i> Ver todas</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
  @endif

    <div class="col-md-2">
      <div class="card metric {{ $canMedicoes ? 'enabled' : 'disabled' }} text-center shadow-sm rounded-4">
        <div class="card-body">
          <i class="fas fa-cogs fa-2x mb-2"></i>
          <h4>{{ $canMedicoes ? number_format($totalPF, 0, ',', '.') : 0 }}</h4>
          <small>Total PF</small>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card metric {{ $canMedicoes ? 'enabled' : 'disabled' }} text-center shadow-sm rounded-4">
        <div class="card-body">
          <i class="fas fa-dollar-sign fa-2x mb-2"></i>
          <h4>{{ $canMedicoes ? 'R$ '.number_format($valorTotal, 2, ',', '.') : 'R$ 0,00' }}</h4>
          <small>Valor Executado</small>
        </div>
      </div>
    </div>
  </div>

  <!-- 🔹 Gráfico Top Projetos -->
  <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-light">
      <h6 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-chart-bar text-primary me-1"></i>Top 5 Projetos (Pontos de Função e UST)
      </h6>
    </div>
    <div class="card-body bg-white">
      <canvas id="graficoTopProjetos"></canvas>
    </div>
  </div>

  <!-- 🔹 Últimos boletins emitidos -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-light">
      <h6 class="mb-0 text-secondary fw-semibold">
        <i class="fas fa-clock text-success me-1"></i>Boletins Recentes
      </h6>
    </div>
    <div class="card-body bg-white p-0">
      <table class="table table-hover dt-skip mb-0">
        <thead class="table-light">
          <tr>
            <th>Nº</th>
            <th>Projeto</th>
            <th>Contrato</th>
            <th>Medição</th>
            <th class="text-end">Valor (R$)</th>
            <th class="text-center">Data</th>
          </tr>
        </thead>
        <tbody>
          @forelse($boletinsRecentes as $b)
            <tr>
              <td>{{ $b->id }}</td>
              <td>{{ $b->projeto->nome ?? '—' }}</td>
              <td>{{ $b->medicao->contrato->numero ?? '—' }}</td>
              <td>{{ $b->medicao->mes_referencia ?? '—' }}</td>
              <td class="text-end">R$ {{ number_format($b->valor_total, 2, ',', '.') }}</td>
              <td class="text-center">{{ $b->data_emissao->format('d/m/Y') }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-3">Nenhum boletim recente encontrado.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctxTopProjetos = document.getElementById('graficoTopProjetos');
    const canProjetos = @json($canProjetos ?? false);
    const labelsRaw = @json(($topProjetos ?? collect())->pluck('projeto'));
    const labels = Array.isArray(labelsRaw) && labelsRaw.length ? labelsRaw : ['Projeto 1','Projeto 2','Projeto 3','Projeto 4','Projeto 5'];

    const pfDataRaw = @json(($topProjetos ?? collect())->pluck('total_pf'));
    const ustDataRaw = @json(($topProjetos ?? collect())->pluck('total_ust'));
    const pfData = (Array.isArray(pfDataRaw) && pfDataRaw.length) ? pfDataRaw : new Array(labels.length).fill(0);
    const ustData = (Array.isArray(ustDataRaw) && ustDataRaw.length) ? ustDataRaw : new Array(labels.length).fill(0);

    const primaryBlue = '#0d6efd'; // Azul padrão Bootstrap
    const successGreen = '#198754'; // Verde padrão Bootstrap

    const chartConfig = canProjetos ? {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pontos de Função (PF)',
                    data: pfData,
                    backgroundColor: primaryBlue,
                    borderColor: primaryBlue,
                    borderWidth: 1
                },
                {
                    label: 'UST',
                    data: ustData,
                    backgroundColor: successGreen,
                    borderColor: successGreen,
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true, grid: { color: '#e9ecef' } }, x: { grid: { color: '#f1f3f5' } } }
        }
    } : {
        // Quando desabilitado, mostra curva em 0, cinza
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pontos de Função (PF)',
                    data: new Array(labels.length).fill(0),
                    borderColor: primaryBlue,
                    backgroundColor: 'transparent',
                    tension: 0.3,
                    borderWidth: 2
                },
                {
                    label: 'UST',
                    data: new Array(labels.length).fill(0),
                    borderColor: successGreen,
                    backgroundColor: 'transparent',
                    tension: 0.3,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true, grid: { color: '#eceff1' } }, x: { grid: { color: '#f1f3f5' } } }
        }
    };

    new Chart(ctxTopProjetos, chartConfig);

    // ====== Utilitários: Filtros, Contadores e Ordenação ======
    function updateCount(rows, countEl) {
      if (!countEl) return;
      const total = rows.length;
      const visible = rows.filter(r => r.style.display !== 'none').length;
      countEl.textContent = `Exibindo ${visible} de ${total}`;
    }

    function sortRows(rows, key, type, dir) {
      const parseVal = (v) => {
        if (type === 'number') {
          const n = parseFloat(String(v).replace(/[^\d.-]/g, ''));
          return isNaN(n) ? 0 : n;
        }
        return String(v || '').toLowerCase();
      };
      return rows.slice().sort((a, b) => {
        const va = parseVal(a.dataset[key]);
        const vb = parseVal(b.dataset[key]);
        if (va < vb) return dir === 'asc' ? -1 : 1;
        if (va > vb) return dir === 'asc' ? 1 : -1;
        return 0;
      });
    }

    function attachSort(modalSelector, countEl) {
      const tbody = document.querySelector(`${modalSelector} table tbody`);
      const headers = document.querySelectorAll(`${modalSelector} thead th.sortable`);
      if (!tbody || !headers.length) return;
      const state = {};
      headers.forEach(th => {
        const key = th.dataset.sort;
        const type = th.dataset.type || 'string';
        const ind = th.querySelector('.sort-ind');
        th.addEventListener('click', () => {
          const rows = Array.from(tbody.querySelectorAll('tr'));
          const dir = state[key] = (state[key] === 'asc' ? 'desc' : 'asc');
          const sorted = sortRows(rows, key, type, dir);
          sorted.forEach(r => tbody.appendChild(r));
          if (ind) ind.textContent = dir === 'asc' ? '▲' : '▼';
          updateCount(sorted, countEl);
        });
      });
    }

    // ====== Filtros dos Modais ======
    function applyFilters(rows, criteriaFn) {
      const crit = criteriaFn();
      rows.forEach(row => {
        const d = row.dataset;
        const text = (crit.text || '').toLowerCase();
        let visible = true;
        if (text) {
          const haystack = [d.numero, d.titulo, d.codigo, d.competencia]
            .filter(Boolean)
            .join(' ') // concatena
            .toLowerCase();
          if (!haystack.includes(text)) visible = false;
        }
        if (crit.situacao && d.situacao !== crit.situacao) visible = false;
        if (crit.tipo && d.tipo !== crit.tipo) visible = false;
        if (crit.status && d.status !== crit.status) visible = false;
        row.style.display = visible ? '' : 'none';
      });
    }

    // Contratos
    const contratosTable = document.querySelector('#modalContratos table tbody');
    if (contratosTable) {
      const rows = Array.from(contratosTable.querySelectorAll('tr'));
      const txt = document.getElementById('filtroContratoTexto');
      const sit = document.getElementById('filtroContratoSituacao');
      const tipo = document.getElementById('filtroContratoTipo');
      const countEl = document.getElementById('contratosCount');
      const run = () => applyFilters(rows, () => ({
        text: txt?.value || '',
        situacao: sit?.value || '',
        tipo: tipo?.value || ''
      })) || updateCount(rows, countEl);
      [txt, sit, tipo].forEach(el => el && el.addEventListener('input', run));
      [sit, tipo].forEach(el => el && el.addEventListener('change', run));
      // Inicializa contador e ordenação
      updateCount(rows, countEl);
      attachSort('#modalContratos', countEl);
    }

    // Projetos
    const projetosTable = document.querySelector('#modalProjetos table tbody');
    if (projetosTable) {
      const rows = Array.from(projetosTable.querySelectorAll('tr'));
      const txt = document.getElementById('filtroProjetoTexto');
      const sit = document.getElementById('filtroProjetoSituacao');
      const status = document.getElementById('filtroProjetoStatus');
      const countEl = document.getElementById('projetosCount');
      const run = () => applyFilters(rows, () => ({
        text: txt?.value || '',
        situacao: sit?.value || '',
        status: status?.value || ''
      })) || updateCount(rows, countEl);
      [txt].forEach(el => el && el.addEventListener('input', run));
      [sit, status].forEach(el => el && el.addEventListener('change', run));
      updateCount(rows, countEl);
      attachSort('#modalProjetos', countEl);
    }

    // Medições
    const medicoesTable = document.querySelector('#modalMedicoes table tbody');
    if (medicoesTable) {
      const rows = Array.from(medicoesTable.querySelectorAll('tr'));
      const txt = document.getElementById('filtroMedicaoTexto');
      const status = document.getElementById('filtroMedicaoStatus');
      const countEl = document.getElementById('medicoesCount');
      const run = () => applyFilters(rows, () => ({
        text: txt?.value || '',
        status: status?.value || ''
      })) || updateCount(rows, countEl);
      [txt].forEach(el => el && el.addEventListener('input', run));
      [status].forEach(el => el && el.addEventListener('change', run));
      updateCount(rows, countEl);
      attachSort('#modalMedicoes', countEl);
    }
  });
</script>
@endsection

@section('css')
<style>
  /* Voltar às cores padrão Bootstrap, removendo o cinza forçado */
  .ui-card.enabled { background-color: #ffffff; border: 1px solid #dee2e6; }
  .ui-card.enabled:hover { background-color: #f8f9fa; }
  .ui-card.disabled { opacity: 0.7; cursor: not-allowed; }

  .card.metric.enabled { background-color: #ffffff; color: #212529; border: 1px solid #dee2e6; }
  .card.metric.enabled[role="button"] { cursor: pointer; }
  .card.metric.disabled { background-color: #ffffff; color: #6c757d; border: 1px solid #dee2e6; }
  thead th.sortable { cursor: pointer; user-select: none; }
  thead th .sort-ind { font-size: 0.8em; color: #6c757d; margin-left: 4px; }
</style>
@endsection
