<?php


use App\Http\Controllers\Api\ProjetoApiController;
use App\Http\Controllers\Api\HostApiController;
use App\Http\Controllers\Api\NocMapController;
use App\Http\Controllers\MapaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HostController;
use App\Http\Controllers\HostTesteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EscolaController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\EmpenhoController;
use App\Http\Controllers\MedicaoController;
use App\Http\Controllers\FuncaoSistemaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\OcorrenciaFiscalizacaoController;
use App\Http\Controllers\MonitoramentoController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OcorrenciaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\DREController;
use App\Http\Controllers\TesteConexaoController;
USE App\Http\Controllers\SituacaoController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\HostDashboardController;
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\ProjetoSoftwareController;
use App\Http\Controllers\ApfController;
use App\Http\Controllers\FiscalizacaoProjetoController;
use App\Http\Controllers\DocumentoProjetoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\ServidorController;
use App\Http\Controllers\BoletimMedicaoController;
use App\Http\Controllers\ProjetoRelacionamentoController;
use App\Http\Controllers\ProjetoWorkflowController;
use App\Http\Controllers\MedicaoDocumentoController;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\AntifraudeDashboardController;
use App\Http\Controllers\ContratoConformidadeController;
use App\Http\Controllers\ContratoInteligenteController;
use App\Http\Controllers\TermoReferenciaController;
use App\Http\Controllers\MedicaoTelcoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\{
    RequisitoController, AtividadeController, CronogramaController, EquipeController
};


// Página inicial (redireciona para login ou dashboard)

Route::get('/', function () {
    return view('site.index');
});
// Home visível a qualquer usuário autenticado
    Route::get('home', [DashboardController::class, 'index'])->name('home');

// Rotas de autenticação
Auth::routes(['register' => false, 'reset' => true]);

Route::middleware(['auth', 'password.expiration'])->group(function() {
    Route::resource('user_profiles', UserProfileController::class);
});


// 🔹 Rotas RESTful (CRUD completo)
Route::resource('escolas', EscolaController::class);
Route::resource('empresas', EmpresaController::class);
Route::resource('hosts', HostController::class);
Route::resource('contratos', ContratoController::class);
Route::resource('medicoes', MedicaoController::class);
Route::resource('funcoes-sistema', FuncaoSistemaController::class);
Route::resource('documentos', DocumentoController::class);
// Visualizador de PDFs e streaming inline
Route::get('documentos/{documento}/visualizar', [DocumentoController::class, 'visualizar'])->name('documentos.visualizar');
Route::get('documentos/{documento}/stream', [DocumentoController::class, 'stream'])->name('documentos.stream');
Route::get('documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
Route::get('documentos/{documento}/print', [DocumentoController::class, 'print'])->name('documentos.print');

// Atalho para abrir PDF do contrato
Route::get('contratos/{contrato}/pdf', [ContratoController::class, 'pdf'])->name('contratos.pdf');
// Cadastro de documentos vinculados ao contrato
Route::get('contratos/{contrato}/documentos/create', [App\Http\Controllers\ContratoController::class, 'createDocumento'])
    ->name('contratos.documentos.create');
Route::resource('ocorrencias-fiscalizacao', OcorrenciaFiscalizacaoController::class);
Route::resource('ocorrencias', OcorrenciaController::class);
Route::resource('projetos', ProjetoController::class);
// API para DataTables da tela de Projetos
Route::get('/api/projetos', [ProjetoController::class, 'getJsonProjetos'])->name('api.projetos');
Route::resource('user_profiles', UserProfileController::class);
 Route::resource('usuarios',UserProfileController::class);
 Route::resource('empresas', EmpresaController::class);
Route::resource('empenhos', EmpenhoController::class);
Route::resource('hosts', HostController::class);
Route::resource('dres', DREController::class);
Route::resource('host_testes', HostTesteController::class)->only(['index', 'show']);
Route::resource('projetos.apfs', ApfController::class); // nested: /projetos/{projeto}/apfs
Route::resource('projetos.fiscalizacoes', FiscalizacaoProjetoController::class)->shallow();
Route::resource('pessoas', PessoaController::class);
Route::resource('servidores', ServidorController::class);
Route::resource('boletins', BoletimMedicaoController::class);
Route::resource('demandas', DemandaController::class);

// Módulo: Contratações
Route::prefix('contratacoes')->name('contratacoes.')->group(function () {
    Route::view('/', 'contratacoes.index')->name('index');
Route::resource('termos-referencia', TermoReferenciaController::class)
        ->parameters(['termos-referencia' => 'tr']);
Route::get('/api/termos-referencia', [TermoReferenciaController::class, 'getJson'])
        ->name('termos-referencia.api');
Route::get('termos-referencia/{tr}/pdf', [TermoReferenciaController::class, 'pdf'])
        ->name('termos-referencia.pdf');
    // Workflow simples de TR: enviar para aprovação e aprovar
    Route::post('termos-referencia/{tr}/enviar-aprovacao', [TermoReferenciaController::class, 'enviarAprovacao'])
        ->middleware(['auth'])
        ->name('termos-referencia.enviar-aprovacao');
    Route::post('termos-referencia/{tr}/aprovar', [TermoReferenciaController::class, 'aprovar'])
        ->middleware(['auth','role:Administrador,Gestor de Contrato'])
        ->name('termos-referencia.aprovar');
    Route::post('termos-referencia/{tr}/retornar-elaboracao', [TermoReferenciaController::class, 'retornarElaboracao'])
        ->middleware(['auth','role:Administrador,Gestor de Contrato'])
        ->name('termos-referencia.retornar-elaboracao');

    // Reprovar (voltar para rascunho com motivo)
    Route::post('termos-referencia/{tr}/reprovar', [TermoReferenciaController::class, 'reprovar'])
        ->middleware(['auth','role:Administrador,Gestor de Contrato'])
        ->name('termos-referencia.reprovar');

    // Itens do Termo de Referência (aninhados, com shallow destroy)
Route::post('termos-referencia/{tr}/itens', [\App\Http\Controllers\TermoReferenciaItemController::class, 'store'])
        ->name('termos-referencia.itens.store');
    Route::put('termos-referencia/itens/{item}', [\App\Http\Controllers\TermoReferenciaItemController::class, 'update'])
        ->name('termos-referencia.itens.update');
    Route::delete('termos-referencia/itens/{item}', [\App\Http\Controllers\TermoReferenciaItemController::class, 'destroy'])
        ->name('termos-referencia.itens.destroy');
});

// 🔹 Rotas testes de rede
// 🔹 Rotas de testes de conexão (pings manuais, diagnóstico)
//Route::get('/teste-conexao', [App\Http\Controllers\HostController::class, 'index'])->name('teste_conexao.index');
//Route::post('/teste-conexao', [App\Http\Controllers\TesteConexaoController::class, 'testar'])->name('teste_conexao.testar');

// 🔹 Rotas de monitoramento automático (CRUD + histórico + teste)
//Route::resource('monitoramentos', MonitoramentoController::class)->except(['show']);
//Route::get('monitoramentos/{id}/testar', [MonitoramentoController::class, 'testar'])->name('monitoramentos.testar');
//Route::get('monitoramentos/{id}/historico', [MonitoramentoController::class, 'historico'])->name('monitoramentos.historico');

Route::get('empenho/{id}/imprimir', [EmpenhoController::class, 'imprimir'])
    ->name('empenho.imprimir');

// CADASTRO DE PERFIS DE USUÁRIOS
Route::get('user_profiles/index', [App\Http\Controllers\UserProfileController::class, 'index'])
->name('user_profiles.index');
Route::get('user_profiles/create', [App\Http\Controllers\UserProfileController::class, 'create'])
->name('user_profiles.create');
Route::get('user_profiles/show', [App\Http\Controllers\UserProfileController::class, 'show'])
->name('user_profiles.show');
// FIM DE USUÁRIOS

// FISCALIZAÇÃO PROJETO DE SOFTWARE
Route::post('fiscalizacoes/{fiscalizacao}/documentos', [DocumentoProjetoController::class,'store'])->name('fiscalizacoes.documentos.store');
// FIM DE PROJETO DE SOFTWARE





// TESTES E MONITORAMENTO DE CONEXÕES

Route::get('/hosts/{id}', [HostController::class, 'show'])->name('api.hosts.show');
Route::get('/hosts.index', [HostController::class, 'index'])->name('hosts.index');
Route::get('/monitoramentos', [MonitoramentoController::class, 'index'])
->name('monitoramentos.index');
Route::get('/host_testes/dashboard', [HostDashboardController::class, 'index'])
->name('host_testes.dashboard');


// 🔹 Execução de teste manual (ping individual)
Route::post('/hosts/{id}/testar', [HostTesteController::class, 'executarTesteManual'])
    ->name('hosts.testar');

// Rotas para o mapa
Route::get('/mapas/escolas', [MapaController::class, 'index'])->name('mapas.escolas');
Route::get('medicoes/{medicao}/telco/mapa', [MedicaoTelcoController::class, 'mapa'])
    ->name('medicoes.telco.mapa');

Route::get('relatorios/gerar', [RelatorioController::class, 'gerar'])->name('relatorios.gerar');
Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
//BOLETIM DE MEDIÇÃO
Route::get('boletins/{id}/pdf', [BoletimMedicaoController::class, 'exportPdf'])->name('boletins.pdf');

//FIM DE BOLETIM DE MEDIÇÃO
// DASHBOARD PROJETOS
Route::get('/dashboard/projetos', [DashboardController::class, 'index'])->name('dashboard.projetos');
Route::get('dashboard/antifraude', [AntifraudeDashboardController::class, 'index'])
    ->name('dashboard.antifraude');
Route::get('/projetos/{projeto}/gantt', [ProjetoController::class, 'gantt'])
    ->name('projetos.gantt');
Route::get('/projetos/{projeto}/dashboard', [ProjetoController::class, 'dashboard'])
    ->name('projetos.dashboard');
    Route::get('/projetos/{projeto}/relatorio/pdf', [ProjetoController::class, 'relatorioPdf'])
    ->name('projetos.relatorio.pdf');
    Route::get('/projetos/index', [ProjetoController::class, 'index'])
    ->name('projetos.index');
Route::get('/projetos/create', [ProjetoController::class, 'create'])
    ->name('projetos.create');

//FINAL DE DASHBOARD PROJETOS
// DASHBOARD MONITORAMENTO DE CONEXÕES
Route::get('/monitoramentos', [MonitoramentoController::class, 'index'])
    ->name('monitoramentos.index');
    Route::get('/monitoramentos/dashboard2', [MonitoramentoController::class, 'dashboard2'])
    ->name('monitoramentos.dashboard2');
    Route::get('/monitoramentos/heatline', [MonitoramentoController::class, 'heatline'])
    ->name('monitoramentos.heatline');
    Route::get('/monitoramentos/matrix', [MonitoramentoController::class, 'matrix'])
    ->name('monitoramentos.matrix');
//FINAL DE DASHBOARD MONITORAMENTO DE CONEXÕES
// INICIO DO NOC
Route::get('/noc/export/pdf', [NocReportController::class, 'pdf'])->name('noc.export.pdf');
Route::get('/noc/export/excel', [NocReportController::class, 'excel'])->name('noc.export.excel');

//FINAL DO NOC


// ROTAS DE PROJETOS
Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('requisitos', [ProjetoRelacionamentoController::class, 'requisitos']);
    Route::get('atividades', [ProjetoRelacionamentoController::class, 'atividades']);
    Route::get('cronograma', [ProjetoRelacionamentoController::class, 'cronograma']);
    Route::get('equipe', [ProjetoRelacionamentoController::class, 'equipe']);
});
//FINAL DA ROTA DE PROJETOS
// ROTAS DE REQUISITOS
Route::post('/requisitos', [RequisitoController::class, 'store'])->name('requisitos.store');
Route::post('/atividades', [AtividadeController::class, 'store'])->name('atividades.store');
Route::post('/cronograma', [CronogramaController::class, 'store'])->name('cronograma.store');
Route::post('/equipe', [EquipeController::class, 'store'])->name('equipe.store');

Route::put('/requisitos/{requisito}', [RequisitoController::class, 'update']);
Route::put('/atividades/{atividade}', [AtividadeController::class, 'update']);
Route::put('/cronograma/{cronograma}', [CronogramaController::class, 'update']);
Route::put('/equipe/{equipe}', [EquipeController::class, 'update']);

Route::delete('/requisitos/{requisito}', [RequisitoController::class, 'destroy']);
Route::delete('/atividades/{atividade}', [AtividadeController::class, 'destroy']);
Route::delete('/cronograma/{cronograma}', [CronogramaController::class, 'destroy']);
Route::delete('/equipe/{equipe}', [EquipeController::class, 'destroy']);
Route::get('/requisitos/{requisito}', [RequisitoController::class, 'show']);
Route::get('/atividades/{atividade}', [AtividadeController::class, 'show']);
Route::get('/cronograma/{cronograma}', [CronogramaController::class, 'show']);
Route::get('/equipe/{equipe}', [EquipeController::class, 'show']);
// FINAL DA ROTA DE REQUISITOS

// inicio das rotas de workflow BPM


Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('workflow', [ProjetoWorkflowController::class, 'show'])->name('projetos.workflow.show');
    Route::post('workflow/iniciar', [ProjetoWorkflowController::class, 'iniciar'])->name('projetos.workflow.iniciar');
    Route::post('workflow/avancar', [ProjetoWorkflowController::class, 'avancar'])->name('projetos.workflow.avancar');
});
Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('workflow', [ProjetoWorkflowController::class, 'show'])->name('projetos.workflow.show');
    Route::post('workflow/iniciar', [ProjetoWorkflowController::class, 'iniciar'])->name('projetos.workflow.iniciar');
    Route::post('workflow/avancar', [ProjetoWorkflowController::class, 'avancar'])->name('projetos.workflow.avancar');
});

//final das rotas de workflow BPM

// inicio rotas workflow medição
Route::post('medicoes/{medicao}/documentos/upload', [MedicaoDocumentoController::class, 'upload'])
    ->name('medicoes.documentos.upload');

Route::post('medicoes/{medicao}/documentos/validar_nf', [MedicaoDocumentoController::class, 'validarNF'])
    ->name('medicoes.documentos.validar_nf');

Route::post('medicoes/{medicao}/documentos/{doc}/revalidar', [MedicaoDocumentoController::class, 'revalidar'])
    ->name('medicoes.documentos.revalidar');

Route::post('medicoes/{medicao}/documentos/substituir_nf', [MedicaoDocumentoController::class, 'substituirNF'])
    ->name('medicoes.documentos.substituir_nf');

// fim das rotas workflow medição
// inicio das rotas de documentos de medição


Route::prefix('medicoes/{medicao}')->group(function () {
    Route::post('documentos/upload',        [MedicaoDocumentoController::class, 'upload'])->name('medicoes.documentos.upload');
    Route::post('documentos/validar-nf',    [MedicaoDocumentoController::class, 'validarNF'])->name('medicoes.documentos.validar_nf');
    Route::post('documentos/{doc}/revalidar',[MedicaoDocumentoController::class, 'revalidar'])->name('medicoes.documentos.revalidar');
    Route::post('documentos/substituir-nf', [MedicaoDocumentoController::class, 'substituirNF'])->name('medicoes.documentos.substituir_nf');
    Route::get('comparacao',               [MedicaoDocumentoController::class, 'comparacao'])->name('medicoes.documentos.comparacao');
});
// final das rotas de documentos de medição
// inicio rotas demandas


Route::post('demandas/{demanda}/requisitos', [DemandaController::class, 'addRequisito'])
    ->name('demandas.requisitos.store');
Route::delete('demandas/{demanda}/requisitos/{requisito}', [DemandaController::class, 'deleteRequisito'])
    ->name('demandas.requisitos.destroy');
//final rotas demandas
// Inicio Rotas Contratos
Route::get('contratos/{contrato}/edit', [ContratoController::class, 'edit'])->name('contratos.edit');
Route::put('contratos/{contrato}', [ContratoController::class, 'update'])->name('contratos.update');
Route::get('dashboard/contratos/conformidade', [ContratoConformidadeController::class, 'index'])
    ->name('dashboard.contratos.conformidade');
// Upload inteligente (IA) – página e processamento opcional
Route::get('contratos/upload', [ContratoInteligenteController::class, 'uploadForm'])->name('contratos.upload');
Route::post('contratos/upload', [ContratoInteligenteController::class, 'receberUpload'])->name('contratos.upload.receber');
Route::post('contratos/salvar', [ContratoInteligenteController::class, 'salvar'])->name('contratos.salvar');
Route::post('contratos/extrair', [ContratoController::class, 'extrair'])->name('contratos.extrair');
// Upload de PDF vinculado a um contrato já existente
Route::post('contratos/{contrato}/pdf', [ContratoController::class, 'uploadPdf'])->name('contratos.pdf.upload');

//Final Rotas contratos
// Rotas de notificação
Route::middleware(['auth'])->group(function () {
    Route::get('/notificacoes', [NotificationController::class, 'index'])
        ->name('notificacoes.index');

    Route::post('/notificacoes/{notificacao}/lida', [NotificationController::class, 'marcarLida'])
        ->name('notificacoes.lida');

    Route::post('/notificacoes/marcar-todas', [NotificationController::class, 'marcarTodas'])
        ->name('notificacoes.todas');

    Route::post('/push/subscribe', function (\Illuminate\Http\Request $request) {
        auth()->user()->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth']
        );
        return ['success' => true];
    })->name('push.subscribe');
});
//final das rotas de notificação


//ROTAS - AJAX
Route::post('/monitoramentos/update', [MonitoramentoController::class, 'atualizar']);
Route::get('/monitoramentos/historico/{id}', [MonitoramentoController::class, 'historico']);


// Hosts que o Python deve monitorar
Route::get('/hosts-monitor', [HostMonitorController::class, 'listarHosts']);

// Python envia os resultados
Route::post('/monitoramentos/update', [MonitoramentoController::class, 'atualizar']);

// Histórico de um host, usado pelos gráficos
Route::get('/monitoramentos/historico/{id}', [MonitoramentoController::class, 'historico']);
Route::get('/monitoramentos/latencia-geral', function () {
    $media = \App\Models\Monitoramento::latest()
        ->take(100)
        ->avg('latencia');

    $series = \App\Models\Monitoramento::latest()
        ->take(20)
        ->pluck('latencia')
        ->toArray();

    return response()->json([
        'media' => $media ?? 0,
        'series' => array_reverse($series)
    ]);
});
Route::get('/monitoramentos/heatmap', function () {
    $hosts = \App\Models\Host::with(['escola.dre', 'monitoramentos' => function($q) {
        $q->orderByDesc('ultima_verificacao')->limit(1);
    }])->get();

    $pontos = $hosts->filter(fn($h) => $h->escola && $h->escola->latitude && $h->escola->longitude)
        ->map(function($h) {
            $m = $h->monitoramentos->first();
            return [
                'host_id'   => $h->id,
                'nome'      => $h->nome_conexao,
                'dre'       => $h->escola->dre->nome ?? null,
                'escola'    => $h->escola->nome ?? null,
                'lat'       => (float) $h->escola->latitude,
                'lng'       => (float) $h->escola->longitude,
                'online'    => $m?->online ?? 0,
                'latencia'  => $m?->latencia,
            ];
        })
        ->values();

    return response()->json($pontos);
})->name('api.monitoramentos.heatmap');

Route::get('/monitoramentos/mikrotik/{host}', function (\App\Models\Host $host) {
    $logs = $host->monitoramentos()
                ->orderByDesc('ultima_verificacao')
                ->limit(50)
                ->get(['ultima_verificacao','rx_rate','tx_rate'])
                ->sortBy('ultima_verificacao'); // reordena asc p/ gráfico

    return response()->json([
        'labels' => $logs->pluck('ultima_verificacao')->map(fn($d) => $d->format('H:i'))->values(),
        'rx'     => $logs->pluck('rx_rate')->values(),
        'tx'     => $logs->pluck('tx_rate')->values(),
    ]);
})->name('api.monitoramentos.mikrotik');
Route::get('/api/monitoramentos/heatline', [MonitoramentoController::class, 'apiHeatline'])
    ->name('api.monitoramentos.heatline');
    Route::get('/api/monitoramentos/matrix', [MonitoramentoController::class, 'apiMatrix'])
    ->name('api.monitoramentos.matrix');
    Route::get('/api/noc/mapa-sla', [NocMapController::class, 'mapaSla'])
     ->name('api.noc.mapa-sla');

Route::get('/api/noc/top-downtime', [NocStatsController::class, 'topDowntime'])
    ->name('api.noc.top-downtime');

Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('apf',        [ProjetoApiController::class, 'apf'])->name('api.projetos.apf');
    Route::get('atividades', [ProjetoApiController::class, 'atividades'])->name('api.projetos.atividades');
    Route::get('medicao',    [ProjetoApiController::class, 'medicao'])->name('api.projetos.medicao');
    Route::get('boletins',   [ProjetoApiController::class, 'boletins'])->name('api.projetos.boletins');

    // Dashboard
    Route::get('dashboard/pf-ust',   [ProjetoApiController::class, 'dashboardPfUst'])->name('api.projetos.dashboard.pf_ust');
    Route::get('dashboard/esforco',  [ProjetoApiController::class, 'dashboardEsforco'])->name('api.projetos.dashboard.esforco');
});


// Rota API para DataTables / AJAX
Route::get('api/hosts', [HostController::class, 'getHostsJson'])->name('api.hosts');
Route::get('/api/contratos/{id}/itens', [HostController::class, 'getItensPorContrato']);
Route::get('empenhos/data', [EmpenhoController::class, 'getData'])->name('empenhos.data');
Route::get('/escolas-data', [EscolaController::class, 'getData'])->name('escolas.data');
Route::get('/hosts/dashboard/data', [HostDashboardController::class, 'dadosAjax'])
    ->name('hosts.dashboard.data');
Route::get('/host_testes/historico', [App\Http\Controllers\HostDashboardController::class, 'historicoAjax'])
->name('host_testes.historico');
Route::get('/api/contratos', [App\Http\Controllers\ContratoController::class, 'getJsonContratos'])
    ->name('api.contratos');
Route::get('/api/contratos/detalhes/{id}', [App\Http\Controllers\ContratoController::class, 'detalhesContrato'])
    ->name('api.contratos.detalhes');
Route::get('/api/situacoes', [App\Http\Controllers\SituacaoContratoController::class, 'listar'])
    ->name('api.situacoes');
Route::get('/api/escolas', [MapaController::class, 'escolasGeoJson'])->name('api.escolas');
Route::get('/api/contratos/{id}/itens', [HostController::class, 'getItensPorContrato'])
    ->name('api.contratos.itens');
Route::get('/ajax/contratos/{id}', [ContratoController::class, 'getContratoJson'])
    ->withoutMiddleware(['auth'])
    ->name('ajax.contrato');
    Route::get('contratos/{id}/itens', [ContratoController::class, 'getItens'])
    ->name('contratos.itens');
// 🔹 Rota auxiliar (JSON de detalhes para modal)
Route::get('/escolas/{id}/detalhes', [EscolaController::class, 'detalhes'])
    ->name('escolas.detalhes');
    // DataTables (lista de servidores)
Route::get('/api/servidores', [ServidorController::class, 'index'])->name('api.servidores.index');
// FINAL DE ROTAS DE API AJAX

