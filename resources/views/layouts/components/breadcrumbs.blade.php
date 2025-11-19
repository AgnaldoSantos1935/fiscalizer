@php
    $routeName = Route::currentRouteName();
    $segments = request()->segments();
    $map = [
        'home' => ['label' => 'Início', 'icon' => 'fas fa-home'],
        'dashboard' => ['label' => 'Painel', 'icon' => 'fas fa-tachometer-alt'],
        'empresas' => ['label' => 'Empresas', 'icon' => 'fas fa-building'],
        'contratos' => ['label' => 'Contratos', 'icon' => 'fas fa-file-signature'],
        'escolas' => ['label' => 'Escolas', 'icon' => 'fas fa-school'],
        'projetos' => ['label' => 'Projetos', 'icon' => 'fas fa-project-diagram'],
        'medicoes' => ['label' => 'Medições', 'icon' => 'fas fa-clipboard-check'],
        'notificacoes' => ['label' => 'Notificações', 'icon' => 'fas fa-bell'],
        'documentos' => ['label' => 'Documentos', 'icon' => 'fas fa-file'],
        'atas' => ['label' => 'Atas', 'icon' => 'fas fa-file-alt'],
        'monitoramentos' => ['label' => 'Monitoramentos', 'icon' => 'fas fa-chart-line'],
        'noc' => ['label' => 'NOC', 'icon' => 'fas fa-network-wired'],
        'relatorios' => ['label' => 'Relatórios', 'icon' => 'fas fa-chart-pie'],
        'hosts' => ['label' => 'Hosts', 'icon' => 'fas fa-server'],
        'dres' => ['label' => 'DREs', 'icon' => 'fas fa-university'],
        'mapas' => ['label' => 'Mapas', 'icon' => 'fas fa-map']
    ];

    $routeParts = $routeName ? explode('.', $routeName) : [];
    $prefixFromRoute = $routeParts[0] ?? null;
    $base = $prefixFromRoute ?: ($segments[0] ?? 'home');
    $normalize = function($str){ return ucfirst(str_replace(['-', '_'], ' ', $str ?? '')); };
    $baseInfo = $map[$base] ?? ['label' => $normalize($base), 'icon' => 'fas fa-folder'];

    $actionMap = [
        'index' => 'Listagem', 'create' => 'Novo', 'edit' => 'Editar', 'show' => 'Detalhes',
        'pdf' => 'PDF', 'upload' => 'Upload', 'salvar' => 'Salvar', 'extrair' => 'Extração',
        'visualizar' => 'Visualizar', 'stream' => 'Stream', 'download' => 'Download', 'print' => 'Imprimir',
        'dashboard' => 'Dashboard', 'gantt' => 'Gantt', 'matrix' => 'Matriz', 'heatline' => 'Heatline', 'mapa' => 'Mapa',
        'gerar' => 'Gerar', 'data' => 'Dados', 'comparacao' => 'Comparação',
        'validar_nf' => 'Validar NF', 'revalidar' => 'Revalidar', 'substituir_nf' => 'Substituir NF',
        'lida' => 'Lida', 'todas' => 'Todas', 'workflow' => 'Workflow', 'iniciar' => 'Iniciar', 'avancar' => 'Avançar',
        'export' => 'Exportar', 'excel' => 'Excel'
    ];

    $lastPart = $routeParts ? end($routeParts) : null;
    $secondPart = $routeParts[1] ?? null;
    $action = 'Listagem';
    if ($lastPart && isset($actionMap[$lastPart])) {
        $action = $actionMap[$lastPart];
    } elseif ($secondPart && isset($actionMap[$secondPart])) {
        $action = $actionMap[$secondPart];
    } elseif (str_ends_with($routeName, '.create')) $action = 'Novo';
    elseif (str_ends_with($routeName, '.edit')) $action = 'Editar';
    elseif (str_ends_with($routeName, '.show')) $action = 'Detalhes';
    elseif (str_ends_with($routeName, '.index')) $action = 'Listagem';
    elseif (isset($segments[1])) $action = $normalize($segments[1]);

    $homeUrl = route('home', [], false) ?? url('/');
    $baseUrl = url('/' . ($segments[0] ?? ''));
@endphp

@php($hasTrail = isset($trail) && is_array($trail) && count($trail) > 0)
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
        <li class="breadcrumb-item">
            <a href="{{ $homeUrl }}" class="text-decoration-none text-primary fw-semibold">
                <i class="fas fa-home me-1"></i> Início
            </a>
        </li>

        @if($hasTrail)
            @foreach($trail as $index => $item)
                @php(
                    $isLast = $index === count($trail) - 1
                )
                @if(!$isLast)
                    <li class="breadcrumb-item">
                        <a href="{{ $item['url'] ?? '#' }}" class="text-decoration-none text-primary fw-semibold">
                            @if(!empty($item['icon']))<i class="{{ $item['icon'] }} me-1"></i>@endif
                            {{ $item['label'] ?? '' }}
                        </a>
                    </li>
                @else
                    <li class="breadcrumb-item active text-secondary fw-semibold" aria-current="page">
                        @if(!empty($item['icon']))<i class="{{ $item['icon'] }} me-1"></i>@endif
                        {{ $item['label'] ?? '' }}
                    </li>
                @endif
            @endforeach
        @else
            @if(!empty($segments))
                <li class="breadcrumb-item">
                    <a href="{{ $baseUrl }}" class="text-decoration-none text-primary fw-semibold">
                        <i class="{{ $baseInfo['icon'] }} me-1"></i> {{ $baseInfo['label'] }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active text-secondary fw-semibold" aria-current="page">{{ $action }}</li>
        @endif
    </ol>
</nav>