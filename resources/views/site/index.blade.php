@extends('layouts.publico')

@section('title','Fiscalizer — Portal Oficial')

@section('content')
<header class="gov-header py-2">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="brand">
      <img src="{{ asset('img/logo/fiscalizer-sistema.png') }}" alt="Fiscalizer" height="32">
      <div class="d-flex flex-column">
        <strong>Governo do Estado de Exemplo</strong>
        <span class="small">SEDUC-EX • Fiscalizer</span>
      </div>
    </div>
    <nav class="d-flex align-items-center gap-3">
      <a href="#sobre" class="text-white text-decoration-none">Sobre o Sistema</a>
      <a href="#legislacao" class="text-white text-decoration-none">Documentos e Legislação</a>
      <a href="#modulos" class="text-white text-decoration-none">Recursos e Módulos</a>
      <a href="{{ route('site.imagens') }}" class="text-white text-decoration-none">Imagens</a>
      <a href="#contato" class="text-white text-decoration-none">Contato / Suporte</a>
      <a href="{{ Route::has('login') ? route('login') : route('home') }}" class="btn btn-acesso btn-sm ms-2">Acessar Sistema</a>
    </nav>
  </div>
</header>

<section id="sobre" class="hero py-5">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1 class="fw-bold">Fiscalizer — Plataforma Integrada de Governança de Contratos</h1>
        <p class="lead mt-2">Transparência, controle e inteligência a serviço da SEDUC-EX</p>
        <div class="mt-3 d-flex gap-2">
          <a href="{{ Route::has('login') ? route('login') : route('home') }}" class="btn btn-light btn-lg"><i class="fa-solid fa-right-to-bracket me-2"></i>Entrar no Sistema</a>
        </div>
        <div class="mt-3 d-flex flex-wrap gap-3">
          <a href="#legislacao" class="text-white text-decoration-none"><i class="fa-solid fa-file-lines me-1"></i> Documentação</a>
          <a href="#modulos" class="text-white text-decoration-none"><i class="fa-solid fa-building me-1"></i> Módulos</a>
          <a href="#recursos" class="text-white text-decoration-none"><i class="fa-solid fa-chart-pie me-1"></i> Recursos</a>
        </div>
      </div>
      @auth
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-6">
                <div class="metric-card rounded-3 p-3 bg-white">
                  <div class="text-muted small">Contratos Vigentes</div>
                  @php
                    $contratosVigentes = \App\Models\Contrato::all()->filter(function ($c) {
                      $inicio = $c->getAttribute('data_inicio')
                        ?? $c->getAttribute('data_inicio_vigencia')
                        ?? $c->getAttribute('data_assinatura');
                      if (! $inicio) { return false; }
                      $final = $c->data_final;
                      $now = \Carbon\Carbon::now();
                      $inicioC = $inicio instanceof \Carbon\Carbon ? $inicio : \Carbon\Carbon::parse($inicio);
                      if ($final) {
                        $finalC = $final instanceof \Carbon\Carbon ? $final : \Carbon\Carbon::parse($final);
                        return $inicioC <= $now && $finalC >= $now;
                      }
                      return $inicioC <= $now;
                    })->count();
                  @endphp
                  <div class="value">{{ $contratosVigentes }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="metric-card rounded-3 p-3 bg-white">
                  <div class="text-muted small">Valor Contratado no Ano</div>
                  @php
                    $anoAtual = (int) date('Y');
                    $valorAno = \App\Models\Contrato::all()->filter(function ($c) use ($anoAtual) {
                      $d = $c->getAttribute('data_inicio')
                        ?? $c->getAttribute('data_inicio_vigencia')
                        ?? $c->getAttribute('data_assinatura');
                      if (! $d) { return false; }
                      $dc = $d instanceof \Carbon\Carbon ? $d : \Carbon\Carbon::parse($d);
                      return (int) $dc->format('Y') === $anoAtual;
                    })->sum('valor_global');
                  @endphp
                  <div class="value">R$ {{ number_format($valorAno ?? 0, 2, ',', '.') }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="metric-card rounded-3 p-3 bg-white">
                  <div class="text-muted small">Percentual Executado</div>
                  @php
                    $anoAtual = (int) date('Y');
                    $totalAno = \App\Models\Contrato::all()->filter(function ($c) use ($anoAtual) {
                      $d = $c->getAttribute('data_inicio')
                        ?? $c->getAttribute('data_inicio_vigencia')
                        ?? $c->getAttribute('data_assinatura');
                      if (! $d) { return false; }
                      $dc = $d instanceof \Carbon\Carbon ? $d : \Carbon\Carbon::parse($d);
                      return (int) $dc->format('Y') === $anoAtual;
                    })->sum('valor_global');
                    $pagoAno = \App\Models\Pagamentos::whereYear('data_pagamento', date('Y'))->sum('valor_pagamento') ?? 0;
                    $percExec = $totalAno > 0 ? round(($pagoAno / $totalAno) * 100) : 0;
                  @endphp
                  <div class="value">{{ $percExec }}%</div>
                </div>
              </div>
              <div class="col-6">
                <div class="metric-card rounded-3 p-3 bg-white">
                  <div class="text-muted small">Medições em análise</div>
                  <div class="value">{{ \App\Models\Medicao::where('status','em_analise')->count() }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="metric-card rounded-3 p-3 bg-white">
                  <div class="text-muted small">Hosts online (última hora)</div>
                  @php
                    $online = \App\Models\Monitoramento::where('ultima_verificacao','>=',now()->subHour())->where('online',1)->distinct('host_id')->count('host_id');
                    $offline = \App\Models\Monitoramento::where('ultima_verificacao','>=',now()->subHour())->where('online',0)->distinct('host_id')->count('host_id');
                  @endphp
                  <div class="value text-success">{{ $online }}</div>
                  <div class="small text-muted">Offline: {{ $offline }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="metric-card rounded-3 p-3 bg-white">
                  <div class="text-muted small">SLA médio (medições)</div>
                  @php
                    $slaVals = \App\Models\Medicao::all()->map(function ($m) {
                      $v = $m->getAttribute('sla_alcancado') ?? $m->getAttribute('sla_geral') ?? null;
                      return is_numeric($v) ? (float) $v : null;
                    })->filter();
                    $sla = (int) round(($slaVals->count() ? $slaVals->avg() : 0));
                  @endphp
                  <div class="value">{{ $sla }}%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @else
      <div class="col-lg-5">
        <img src="{{ asset('img/govdigital/governanca-digital.png') }}" alt="Governança Digital" class="img-fluid mb-3" onerror="this.style.display='none'">
        <div class="hero-benefits">
          <h5 class="mb-2">Por que usar o Fiscalizer?</h5>
          <ul>
            <li><i class="fa-solid fa-check icon"></i><span>Transparência e governança de contratos</span></li>
            <li><i class="fa-solid fa-check icon"></i><span>Integração e interoperabilidade via APIs</span></li>
            <li><i class="fa-solid fa-check icon"></i><span>Painéis e BI para tomada de decisão</span></li>
            <li><i class="fa-solid fa-check icon"></i><span>Monitoramento de SLAs e prazos</span></li>
            <li><i class="fa-solid fa-check icon"></i><span>Conformidade com a Lei 14.133</span></li>
          </ul>
        </div>
      </div>
      @endauth
    </div>
  </div>
</section>

<section id="governanca-digital" class="py-5">
  <div class="container">
    <div class="row align-items-center g-3">
      <div class="col-md-6">
        <h3 class="section-title mb-3">Governança Digital</h3>
        <p class="mb-2">Tecnologia a serviço do cidadão: integração com gov.br, assinaturas eletrônicas e interoperabilidade para processos mais ágeis, seguros e transparentes.</p>
        <div class="small text-muted">Apoio à Lei 14.129 (Governo Digital) e boas práticas de gestão pública.</div>
      </div>
      <div class="col-md-6">
        <img src="{{ asset('img/govdigital/governanca-digital.png') }}" alt="Governança Digital" class="img-fluid rounded shadow-sm" onerror="this.style.display='none'">
      </div>
    </div>
  </div>
</section>

<section id="identidade-digital" class="py-4 bg-white">
  <div class="container">
    <div class="row align-items-center g-3">
      <div class="col-md-6">
        <h5 class="section-title mb-2">Identidade Digital (gov.br)</h5>
        <p class="mb-0">Login único com níveis de segurança, perfis de acesso e registro de consentimentos, integrando-se aos serviços do Governo Digital.</p>
      </div>
      <div class="col-md-6">
        <img src="{{ asset('img/govbr/usuarios.png') }}" alt="Acesso gov.br" class="img-fluid rounded shadow-sm" onerror="this.style.display='none'">
      </div>
    </div>
  </div>
</section>

<section id="legislacao" class="py-5 bg-white">
  <div class="container">
    <h3 class="section-title mb-3">Atendimento à Legislação</h3>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <i class="fa-solid fa-scale-balanced text-primary"></i>
            <h6 class="mt-2">Lei 14.133/2021</h6>
            <div class="small text-muted">Fases, prazos, matriz de risco e trilhas de auditoria.</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <i class="fa-solid fa-eye text-success"></i>
            <h6 class="mt-2">Lei de Acesso à Informação</h6>
            <div class="small text-muted">Portal de integridade, dados abertos e relatórios públicos.</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <i class="fa-solid fa-user-shield text-warning"></i>
            <h6 class="mt-2">LGPD (Lei 13.709)</h6>
            <div class="small text-muted">Perfis, mínimos necessários, registro de acessos e consentimentos.</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <i class="fa-solid fa-cloud text-info"></i>
            <h6 class="mt-2">Governo Digital (Lei 14.129)</h6>
            <div class="small text-muted">Assinaturas digitais, APIs e integração com gov.br.</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <i class="fa-solid fa-file-shield text-danger"></i>
            <h6 class="mt-2">Conformidade e Auditoria</h6>
            <div class="small text-muted">RBAC, logs completos, evidências e relatórios de conformidade.</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <i class="fa-solid fa-clipboard-check text-secondary"></i>
            <h6 class="mt-2">Boas Práticas e Normativos</h6>
            <div class="small text-muted">Modelos oficiais, prazos automáticos e notificações orientadas.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="recursos" class="py-5 bg-white">
  <div class="container">
    <h3 class="section-title mb-3">O que o Fiscalizer faz?</h3>
    <div class="row g-3">
      <div class="col-md-3">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/about/pessoa-com-relatorio.png') }}" alt="Gestão de Contratos" class="card-media" onerror="this.style.display='none'" />
            <h6 class="mt-2">Gestão de Contratos</h6>
            <div class="small text-muted">Cadastro, vigência, anexos e prazos automáticos.</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/transparencia/transparencia.png') }}" alt="Medições e Ordens de Serviço" class="card-media" />
            <h6 class="mt-2">Medições e Ordens de Serviço</h6>
            <div class="small text-muted">Ciclos, boletins e execução contratual.</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/monitoramento/monitoramento.png') }}" alt="Monitoramento de SLA e prazos" class="card-media" />
            <h6 class="mt-2">Monitoramento de SLA e prazos</h6>
            <div class="small text-muted">Alertas e acompanhamento por metas.</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-outline module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/monitoramento/sla.png') }}" alt="Dashboard inteligente (IA + BI)" class="card-media" onerror="this.style.display='none'" />
            <h6 class="mt-2">Dashboard inteligente (IA + BI)</h6>
            <div class="small text-muted">Painéis dinâmicos e insights automáticos.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="modulos" class="py-5">
  <div class="container">
    <h3 class="section-title mb-3">Módulos do sistema</h3>
    <div class="row g-3">
      <div class="col-sm-6 col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/about/pessoa-com-relatorio.png') }}" alt="Contratos" class="card-media" onerror="this.style.display='none'" />
            <div class="mt-2">Contratos</div>
            <div class="small text-muted">Gestão completa e trilhas de auditoria.</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/empresas/empresas.svg') }}" alt="Empresas" class="card-media" onerror="this.style.display='none'" />
            <div class="mt-2">Empresas</div>
            <div class="small text-muted">Cadastro, perfis e histórico contratual.</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/transparencia/transparencia.png') }}" alt="Empenhos e Pagamentos" class="card-media" />
            <div class="mt-2">Empenhos e Pagamentos</div>
            <div class="small text-muted">Execução financeira e conciliação.</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/transparencia/transparencia.png') }}" alt="Boletins de Medição" class="card-media" />
            <div class="mt-2">Boletins de Medição</div>
            <div class="small text-muted">Resultados e indicadores de execução.</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/monitoramento/monitoramento.png') }}" alt="Host Monitoring" class="card-media" />
            <div class="mt-2">Host Monitoring</div>
            <div class="small text-muted">Disponibilidade, latência e status de serviços.</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/hero/hero-5/hero-img.svg') }}" alt="Fiscalizer-IA" class="card-media" onerror="this.style.display='none'" />
            <div class="mt-2">Fiscalizer-IA</div>
            <div class="small text-muted">Assistente e análises preditivas.</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/docs/etp-tr-minutas.svg') }}" alt="ETP / TR / Minutas" class="card-media" onerror="this.style.display='none'" />
            <div class="mt-2">ETP / TR / Minutas</div>
            <div class="small text-muted">Modelos e geração automatizada de documentos.</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/transparencia/transparencia.png') }}" alt="Ciclo de vida de compras" class="card-media" />
            <div class="mt-2">Ciclo de vida de compras</div>
            <div class="small text-muted">Fases e prazos da Lei 14.133.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="publicos" class="py-5 bg-white">
  <div class="container">
    <div class="row g-4">
      <div class="col-12">
        <img src="{{ asset('img/about/about-4/about-img.svg') }}" alt="Guia e boas práticas" class="img-fluid mb-3" onerror="this.style.display='none'">
      </div>
      <div class="col-md-6">
        <h4 class="section-title">Para Fiscais e Gestores</h4>
        <div class="list-group">
          <div class="list-group-item">Guia do Fiscal — IN 024/2023</div>
          <div class="list-group-item">Minutas oficiais (TR, OS, ETP, relatórios)</div>
          <div class="list-group-item">Normativos aplicáveis</div>
          <div class="list-group-item">Prazos automáticos da Lei 14.133</div>
        </div>
      </div>
      <div class="col-md-6">
        <h4 class="section-title">Para Empresas Contratadas</h4>
        <div class="list-group">
          <div class="list-group-item">Como acessar o sistema</div>
          <div class="list-group-item">Responder OS digitalmente</div>
          <div class="list-group-item">Enviar medições e anexos</div>
          <div class="list-group-item">Histórico de pendências</div>
        </div>
      </div>
    </div>
  </div>
</section>

@auth
<section id="integridade" class="py-5">
  <div class="container">
    <h3 class="section-title mb-3">Portal de Integridade e Transparência</h3>
    <img src="{{ asset('img/transparencia/transparencia.png') }}" alt="Transparência" class="img-fluid mb-3" onerror="this.style.display='none'">
    <div class="row g-3">
      <div class="col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/about/pessoa-com-relatorio.png') }}" alt="Contratos vigentes" class="card-media" onerror="this.style.display='none'" />
            <div class="mt-2">Contratos vigentes</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/transparencia/transparencia.png') }}" alt="Histórico de medições" class="card-media" />
            <div class="mt-2">Histórico de medições</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/transparencia/transparencia.png') }}" alt="Pagamentos" class="card-media" />
            <div class="mt-2">Pagamentos</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card module-card h-100">
          <div class="card-body">
            <img src="{{ asset('img/monitoramento/sla.png') }}" alt="Indicadores de conformidade" class="card-media" onerror="this.style.display='none'" />
            <div class="mt-2">Indicadores de conformidade</div>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-3">
      <a href="https://www.compras.gov.br" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm me-2"><i class="fa-solid fa-globe me-1"></i> Compras.gov.br</a>
      <a href="https://www.gov.br" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm me-2"><i class="fa-solid fa-user-shield me-1"></i> gov.br Login</a>
      <a href="https://www.gov.br/compras/pt-br" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-link me-1"></i> SIASG</a>
    </div>
  </div>
</section>
@endauth

<section id="integracoes" class="py-4 bg-white">
  <div class="container">
    <h5 class="section-title mb-2">Integrações e interoperabilidade</h5>
    <div class="d-flex flex-wrap gap-2">
      <a href="https://www.compras.gov.br" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-globe me-1"></i> Compras.gov.br</a>
      <a href="https://www.gov.br" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-user-shield me-1"></i> gov.br Login</a>
      <a href="https://www.gov.br/compras/pt-br" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-link me-1"></i> SIASG</a>
    </div>
    <img src="{{ asset('img/clients/brands.svg') }}" alt="Parceiros e integrações" class="img-fluid mt-2" onerror="this.style.display='none'">
  </div>
</section>

<section id="contato" class="py-5">
  <div class="container">
    <h3 class="section-title mb-3">Contato</h3>
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row g-3">
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form method="POST" action="{{ route('site.contato.enviar') }}">
              @csrf
              <div class="mb-2">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Mensagem</label>
                <textarea name="mensagem" class="form-control" rows="4" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h5 class="mb-3">Chat de Dúvidas</h5>
            <div id="chatbox" class="border rounded p-2 mb-2" style="height:220px; overflow:auto;"></div>
            <div class="input-group">
              <input id="chatInput" type="text" class="form-control" placeholder="Digite sua pergunta">
              <button id="chatSend" class="btn btn-outline-primary" type="button">Enviar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="planos" class="py-5 bg-white">
  <div class="container">
    <h3 class="section-title mb-3">Planos e Assinatura</h3>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card h-100 module-card">
          <div class="card-body">
            <h5>Essencial</h5>
            <p>Gestão básica de contratos e medições.</p>
            <div class="small text-muted">Ideal para pilotos e pequenas equipes.</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 module-card">
          <div class="card-body">
            <h5>Profissional</h5>
            <p>BI, monitoramento de SLAs e integrações.</p>
            <div class="small text-muted">Recomendado para gestão institucional.</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 module-card">
          <div class="card-body">
            <h5>Governo</h5>
            <p>Conformidade avançada, auditoria e interoperabilidade.</p>
            <div class="small text-muted">Voltado a órgãos com alto volume.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const chatBox = document.getElementById('chatbox');
  const chatInput = document.getElementById('chatInput');
  const chatSend = document.getElementById('chatSend');
  try {
    fetch('{{ route('site.chatbot.ask') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ reset: true })
    }).catch(function(){});
  } catch (e) {}
  function appendMsg(text, who) {
    const div = document.createElement('div');
    div.className = who === 'user' ? 'text-end mb-1' : 'text-start mb-1';
    div.textContent = (who === 'user' ? 'Você: ' : 'Assistente: ') + text;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
  }
  chatSend.addEventListener('click', async function () {
    const q = chatInput.value.trim();
    if (!q) return;
    appendMsg(q, 'user');
    chatInput.value = '';
    try {
      const resp = await fetch('{{ route('site.chatbot.ask') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ pergunta: q })
      });
      const data = await resp.json();
      appendMsg(data.resposta || 'Sem resposta disponível.', 'bot');
      if (Array.isArray(data.sugestoes) && data.sugestoes.length) {
        const wrap = document.createElement('div');
        wrap.className = 'text-start mb-2';
        const label = document.createElement('div');
        label.textContent = 'Sugestões:';
        wrap.appendChild(label);
        data.sugestoes.slice(0,3).forEach(function(s){
          const b = document.createElement('button');
          b.className = 'btn btn-sm btn-outline-primary me-1 mt-1';
          b.textContent = s;
          b.addEventListener('click', function(){ chatInput.value = s; chatSend.click(); });
          wrap.appendChild(b);
        });
        chatBox.appendChild(wrap);
        chatBox.scrollTop = chatBox.scrollHeight;
      }
      if (Array.isArray(data.hist_preview) && data.hist_preview.length) {
        const h = document.createElement('div');
        h.className = 'text-start mb-2';
        const lbl = document.createElement('div');
        lbl.textContent = 'Últimas:';
        h.appendChild(lbl);
        data.hist_preview.forEach(function(item){
          const line = document.createElement('div');
          line.className = 'small';
          line.textContent = (item.t || '') + ' • ' + (item.q || '');
          h.appendChild(line);
        });
        chatBox.appendChild(h);
        chatBox.scrollTop = chatBox.scrollHeight;
      }
    } catch (e) {
      appendMsg('Erro ao enviar a mensagem.', 'bot');
    }
  });
});
</script>
@endsection
