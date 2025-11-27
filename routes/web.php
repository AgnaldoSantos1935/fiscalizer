<?php

use App\Http\Controllers\AntifraudeDashboardController;
use App\Http\Controllers\ApfController;
use App\Http\Controllers\Api\ProjetoApiController;
use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\BoletimMedicaoController;
use App\Http\Controllers\ContratoConformidadeController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ContratoInteligenteController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocumentoProjetoController;
use App\Http\Controllers\DREController;
use App\Http\Controllers\EmpenhoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\EscolaController;
use App\Http\Controllers\FiscalizacaoProjetoController;
use App\Http\Controllers\FluxoOrdemServicoController;
use App\Http\Controllers\FuncaoSistemaController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\HostDashboardController;
use App\Http\Controllers\HostMonitorController;
use App\Http\Controllers\HostTesteController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\MedicaoController;
use App\Http\Controllers\MedicaoDocumentoController;
use App\Http\Controllers\MedicaoTelcoController;
use App\Http\Controllers\MonitoramentoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OcorrenciaController;
use App\Http\Controllers\OcorrenciaFiscalizacaoController;
use App\Http\Controllers\PagamentosController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\ProjetoRelacionamentoController;
use App\Http\Controllers\ProjetoWorkflowController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\RequisitoController;
use App\Http\Controllers\ServidorController;
use App\Http\Controllers\SituacaoContratoController;
use App\Http\Controllers\TermoReferenciaController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\InventarioController as InvController;
use App\Http\Controllers\TelemetriaDashboardController;

// Página inicial (redireciona para login ou dashboard)

Route::get('/', function () {
    return view('site.index');
});
// Home visível a qualquer usuário autenticado
Route::get('home', [DashboardController::class, 'index'])->name('home');

// Atalho enxuto para abrir diretamente o perfil do usuário autenticado
Route::get('meu-perfil', [UserProfileController::class, 'me'])
    ->middleware('auth')
    ->name('user_profiles.me');

// Rotas de autenticação
Auth::routes(['register' => false, 'reset' => true]);

Route::middleware(['auth', 'password.expiration'])->group(function () {
    Route::resource('user_profiles', UserProfileController::class)
        ->middleware('can:view-index-user_profiles');
});

// 🔹 Rotas RESTful (CRUD completo)
Route::resource('escolas', EscolaController::class);
Route::resource('empresas', EmpresaController::class);
// Endpoint JSON para DataTables (Empresas)
Route::get('empresas/data', [EmpresaController::class, 'data'])->name('empresas.data');
// Verificação de CNPJ (AJAX/GET)
Route::get('empresas/verificar', [EmpresaController::class, 'verificar'])->name('empresas.verificar');
Route::resource('hosts', HostController::class);
Route::resource('contratos', ContratoController::class);
Route::resource('medicoes', MedicaoController::class);
// Endpoint JSON para DataTables (Medições)
Route::get('medicoes/data', [MedicaoController::class, 'data'])->name('medicoes.data');
Route::resource('funcoes-sistema', FuncaoSistemaController::class);
Route::resource('documentos', DocumentoController::class);
Route::get('documentos/data', [DocumentoController::class, 'data'])->name('documentos.data');
// Visualizador de PDFs e streaming inline
Route::get('documentos/{documento}/visualizar', [DocumentoController::class, 'visualizar'])->name('documentos.visualizar');
Route::get('documentos/{documento}/stream', [DocumentoController::class, 'stream'])->name('documentos.stream');
Route::get('documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
Route::get('documentos/{documento}/print', [DocumentoController::class, 'print'])->name('documentos.print');

// Atalho para abrir PDF do contrato
Route::get('contratos/{contrato}/pdf', [ContratoController::class, 'pdf'])->name('contratos.pdf');
// Cadastro de documentos vinculados ao contrato
Route::get('contratos/{contrato}/documentos/create', [App\Http\Controllers\ContratoController::class, 'createDocumento'])
    ->middleware('can.action:contratos.anexar_documento')
    ->name('contratos.documentos.create');
Route::resource('ocorrencias-fiscalizacao', OcorrenciaFiscalizacaoController::class);
Route::resource('ocorrencias', OcorrenciaController::class);
Route::resource('projetos', ProjetoController::class);
// API para DataTables da tela de Projetos
Route::get('/api/projetos', [ProjetoController::class, 'getJsonProjetos'])->name('api.projetos');
Route::resource('user_profiles', UserProfileController::class)
    ->middleware('can:view-index-user_profiles');
Route::resource('usuarios', UserProfileController::class)
    ->middleware('can:view-index-user_profiles');

// RBAC: Actions CRUD
Route::resource('actions', App\Http\Controllers\ActionController::class);
// RBAC: Gestão de vínculo Role × Action
Route::get('rbac/roles-actions', [App\Http\Controllers\RoleActionController::class, 'index'])
    ->middleware('can.action:system.admin')
    ->name('rbac.roles_actions.index');
Route::post('rbac/roles-actions/{role}', [App\Http\Controllers\RoleActionController::class, 'update'])
    ->middleware('can.action:system.admin')
    ->name('rbac.roles_actions.update');
Route::resource('empresas', EmpresaController::class);
// Rota explícita para POST de criação de empenhos para evitar conflitos no ambiente de teste
Route::post('empenhos', [EmpenhoController::class, 'store'])->name('empenhos.store');
Route::resource('empenhos', EmpenhoController::class);
// PDF de Pretensão de Empenho (submissão ao gestor do contrato)
Route::get('empenhos/{id}/pretensao/pdf', [EmpenhoController::class, 'pretensaoPdf'])->name('empenhos.pretensao_pdf');
// Upload do PDF emitido (finaliza etapa Emitido)
Route::post('empenhos/{id}/emitido/upload', [EmpenhoController::class, 'uploadEmitidoPdf'])->name('empenhos.emitido_upload');
Route::post('empenhos/{id}/pretensao/solicitar', [EmpenhoController::class, 'solicitarPretensao'])->name('empenhos.pretensao_solicitar');
// Nova rota para salvar solicitação via formulário
Route::post('empenhos/{id}/solicitacao', [EmpenhoController::class, 'salvarSolicitacao'])->name('empenhos.solicitacao_salvar');
// Upload do comprovante de liquidação (finaliza etapa Pago)
Route::post('empenhos/{id}/pago/upload', [EmpenhoController::class, 'uploadComprovanteLiquidacao'])->name('empenhos.pago_upload');
// Registro de Empenho a partir de Pretensão (solicitacoes_empenho)
Route::get('financeiro/solicitacoes-empenho/{solicitacao}/registrar', [EmpenhoController::class, 'registrarFromSolicitacaoForm'])
    ->middleware(['auth', 'can.action:financeiro.registrar_empenho'])
    ->name('financeiro.solicitacoes.registrar_empenho.form');
Route::post('financeiro/solicitacoes-empenho/{solicitacao}/registrar', [EmpenhoController::class, 'registrarFromSolicitacaoStore'])
    ->middleware(['auth', 'can.action:financeiro.registrar_empenho'])
    ->name('financeiro.solicitacoes.registrar_empenho.store');
// Registro de Pagamento para um Empenho
Route::get('financeiro/empenhos/{empenho}/pagamentos/create', [PagamentosController::class, 'create'])
    ->middleware(['auth', 'can.action:financeiro.registrar_pagamento'])
    ->name('financeiro.pagamentos.create');
Route::post('financeiro/empenhos/{empenho}/pagamentos', [PagamentosController::class, 'store'])
    ->middleware(['auth', 'can.action:financeiro.registrar_pagamento'])
    ->name('financeiro.pagamentos.store');
// Aprovar solicitação e gerar PDF/Documento
Route::post('empenhos/solicitacoes/{solicitacao}/aprovar', [EmpenhoController::class, 'aprovarSolicitacao'])
    ->middleware(['auth', 'role:Administrador,Gestor de Contrato'])
    ->name('empenhos.solicitacoes.aprovar');
Route::resource('hosts', HostController::class);
Route::resource('dres', DREController::class);
Route::resource('host_testes', HostTesteController::class)->only(['index', 'show']);
Route::resource('situacoes', SituacaoContratoController::class);
Route::resource('projetos.apfs', ApfController::class); // nested: /projetos/{projeto}/apfs
Route::resource('projetos.fiscalizacoes', FiscalizacaoProjetoController::class)->shallow();
Route::resource('servidores', ServidorController::class);
Route::resource('boletins', BoletimMedicaoController::class);
Route::resource('demandas', DemandaController::class);

// Atas de Registro de Preços
Route::resource('atas', \App\Http\Controllers\AtaRegistroPrecoController::class);
Route::post('atas/{ata}/adesoes', [\App\Http\Controllers\AtaRegistroPrecoController::class, 'storeAdesao'])
    ->name('atas.adesoes.store');
Route::post('adesoes/{adesao}/gerar-pdf', [\App\Http\Controllers\AtaRegistroPrecoController::class, 'gerarAutorizacaoPdf'])
    ->name('adesoes.gerar_pdf');
Route::post('adesoes/{adesao}/status', [\App\Http\Controllers\AtaRegistroPrecoController::class, 'atualizarStatusAdesao'])
    ->name('adesoes.status');

// Módulo: Contratações
Route::prefix('contratacoes')->name('contratacoes.')->group(function () {
    Route::view('/', 'contratacoes.index')->name('index');
    Route::resource('termos-referencia', TermoReferenciaController::class)
        ->parameters(['termos-referencia' => 'tr']);
    Route::get('/api/termos-referencia', [TermoReferenciaController::class, 'getJson'])
        ->name('termos-referencia.api');
    Route::get('termos-referencia/{tr}/pdf', [TermoReferenciaController::class, 'pdf'])
        ->name('termos-referencia.pdf');
    Route::get('termos-referencia/{tr}/docx', [TermoReferenciaController::class, 'docx'])
        ->name('termos-referencia.docx');
    // Workflow simples de TR: enviar para aprovação e aprovar
    Route::post('termos-referencia/{tr}/enviar-aprovacao', [TermoReferenciaController::class, 'enviarAprovacao'])
        ->middleware(['auth'])
        ->name('termos-referencia.enviar-aprovacao');
    Route::post('termos-referencia/{tr}/aprovar', [TermoReferenciaController::class, 'aprovar'])
        ->middleware(['auth', 'role:Administrador,Gestor de Contrato'])
        ->name('termos-referencia.aprovar');
    Route::post('termos-referencia/{tr}/retornar-elaboracao', [TermoReferenciaController::class, 'retornarElaboracao'])
        ->middleware(['auth', 'role:Administrador,Gestor de Contrato'])
        ->name('termos-referencia.retornar-elaboracao');

    // Reprovar (voltar para rascunho com motivo)
    Route::post('termos-referencia/{tr}/reprovar', [TermoReferenciaController::class, 'reprovar'])
        ->middleware(['auth', 'role:Administrador,Gestor de Contrato'])
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
// Route::get('/teste-conexao', [App\Http\Controllers\HostController::class, 'index'])->name('teste_conexao.index');
// Route::post('/teste-conexao', [App\Http\Controllers\TesteConexaoController::class, 'testar'])->name('teste_conexao.testar');

// 🔹 Rotas de monitoramento automático (CRUD + histórico + teste)
// Route::resource('monitoramentos', MonitoramentoController::class)->except(['show']);
// Route::get('monitoramentos/{id}/testar', [MonitoramentoController::class, 'testar'])->name('monitoramentos.testar');
// Route::get('monitoramentos/{id}/historico', [MonitoramentoController::class, 'historico'])->name('monitoramentos.historico');

Route::get('empenho/{id}/imprimir', [EmpenhoController::class, 'imprimir'])
    ->name('empenho.imprimir');

// Ajuste para ambiente de teste onde a base URL inclui "/fiscalizer/public"
if (app()->environment('testing')) {
    Route::post('fiscalizer/public/empenhos', [EmpenhoController::class, 'store']);
}

// CADASTRO DE PERFIS DE USUÁRIOS
Route::get('user_profiles/index', [App\Http\Controllers\UserProfileController::class, 'index'])
    ->middleware('can:view-index-user_profiles')
    ->name('user_profiles.index');
Route::get('user_profiles/create', [App\Http\Controllers\UserProfileController::class, 'create'])
    ->middleware('can:view-create-user_profiles')
    ->name('user_profiles.create');
Route::get('user_profiles/show', [App\Http\Controllers\UserProfileController::class, 'show'])
    ->middleware('can:view-index-user_profiles')
    ->name('user_profiles.show');
// FIM DE USUÁRIOS

// FISCALIZAÇÃO PROJETO DE SOFTWARE
Route::post('fiscalizacoes/{fiscalizacao}/documentos', [DocumentoProjetoController::class, 'store'])->name('fiscalizacoes.documentos.store');
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
// BOLETIM DE MEDIÇÃO
Route::get('boletins/{id}/pdf', [BoletimMedicaoController::class, 'exportPdf'])->name('boletins.pdf');

// FIM DE BOLETIM DE MEDIÇÃO

Route::get('hotwire', function () {
    return view('hotwire.test');
})->name('hotwire.test');

Route::get('hotwire/partial', function (Request $request) {
    $count = (int) $request->query('count', 1);
    return response()->view('hotwire.partial', ['count' => $count]);
})->name('hotwire.partial');
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

// FINAL DE DASHBOARD PROJETOS
// DASHBOARD MONITORAMENTO DE CONEXÕES
Route::get('/monitoramentos', [MonitoramentoController::class, 'index'])
    ->name('monitoramentos.index');
Route::get('/monitoramentos/dashboard2', [MonitoramentoController::class, 'dashboard2'])
    ->name('monitoramentos.dashboard2');
Route::get('/monitoramentos/heatline', [MonitoramentoController::class, 'heatline'])
    ->name('monitoramentos.heatline');
Route::get('/monitoramentos/matrix', [MonitoramentoController::class, 'matrix'])
    ->name('monitoramentos.matrix');
// FINAL DE DASHBOARD MONITORAMENTO DE CONEXÕES
// INICIO DO NOC
// Rotas de exportação do NOC (somente se o controlador existir)
if (class_exists(\App\Http\Controllers\NocReportController::class)) {
    Route::get('/noc/export/pdf', [\App\Http\Controllers\NocReportController::class, 'pdf'])->name('noc.export.pdf');
    Route::get('/noc/export/excel', [\App\Http\Controllers\NocReportController::class, 'excel'])->name('noc.export.excel');
}

// FINAL DO NOC

// ROTAS DE PROJETOS
Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('requisitos', [ProjetoRelacionamentoController::class, 'requisitos']);
    Route::get('atividades', [ProjetoRelacionamentoController::class, 'atividades']);
    Route::get('cronograma', [ProjetoRelacionamentoController::class, 'cronograma']);
    Route::get('equipe', [ProjetoRelacionamentoController::class, 'equipe']);
});
// FINAL DA ROTA DE PROJETOS
// Rotas públicas para upload de documento técnico pela empresa
Route::get('empresa/upload/{token}', [FluxoOrdemServicoController::class, 'formUploadEmpresa'])
    ->name('empresa.upload_documento');
Route::post('empresa/upload/{token}', [FluxoOrdemServicoController::class, 'receberDocumentoEmpresa'])
    ->name('empresa.upload_documento_post');
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

// final das rotas de workflow BPM

// inicio rotas workflow medição
Route::post('medicoes/{medicao}/documentos/upload', [MedicaoDocumentoController::class, 'upload'])
    ->middleware('can.action:medicoes_validar')
    ->name('medicoes.documentos.upload');

Route::post('medicoes/{medicao}/documentos/validar_nf', [MedicaoDocumentoController::class, 'validarNF'])
    ->middleware('can.action:medicoes_validar')
    ->name('medicoes.documentos.validar_nf');

Route::post('medicoes/{medicao}/documentos/{doc}/revalidar', [MedicaoDocumentoController::class, 'revalidar'])
    ->middleware('can.action:medicoes_validar')
    ->name('medicoes.documentos.revalidar');

Route::post('medicoes/{medicao}/documentos/substituir_nf', [MedicaoDocumentoController::class, 'substituirNF'])
    ->middleware('can.action:medicoes_validar')
    ->name('medicoes.documentos.substituir_nf');

// fim das rotas workflow medição
// inicio das rotas de documentos de medição

Route::prefix('medicoes/{medicao}')->group(function () {
    Route::post('documentos/upload', [MedicaoDocumentoController::class, 'upload'])
        ->middleware('can.action:medicoes_validar')
        ->name('medicoes.documentos.upload');
    Route::post('documentos/validar-nf', [MedicaoDocumentoController::class, 'validarNF'])
        ->middleware('can.action:medicoes_validar')
        ->name('medicoes.documentos.validar_nf');
    Route::post('documentos/{doc}/revalidar', [MedicaoDocumentoController::class, 'revalidar'])
        ->middleware('can.action:medicoes_validar')
        ->name('medicoes.documentos.revalidar');
    Route::post('documentos/substituir-nf', [MedicaoDocumentoController::class, 'substituirNF'])
        ->middleware('can.action:medicoes_validar')
        ->name('medicoes.documentos.substituir_nf');
    Route::get('comparacao', [MedicaoDocumentoController::class, 'comparacao'])->name('medicoes.documentos.comparacao');
});
// final das rotas de documentos de medição
// inicio rotas demandas

Route::post('demandas/{demanda}/requisitos', [DemandaController::class, 'addRequisito'])
    ->middleware('can.action:projetos.editar_requisitos')
    ->name('demandas.requisitos.store');
Route::delete('demandas/{demanda}/requisitos/{requisito}', [DemandaController::class, 'deleteRequisito'])
    ->middleware('can.action:projetos.editar_requisitos')
    ->name('demandas.requisitos.destroy');
// final rotas demandas
// Inicio Rotas Contratos
Route::get('contratos/{contrato}/edit', [ContratoController::class, 'edit'])
    ->middleware('can.action:contratos.edit')
    ->name('contratos.edit');
Route::put('contratos/{contrato}', [ContratoController::class, 'update'])
    ->middleware('can.action:contratos.edit')
    ->name('contratos.update');
Route::get('dashboard/contratos/conformidade', [ContratoConformidadeController::class, 'index'])
    ->name('dashboard.contratos.conformidade');
// Upload inteligente (IA) – página e processamento opcional
Route::get('contratos/upload', [ContratoInteligenteController::class, 'uploadForm'])->name('contratos.upload');
Route::post('contratos/upload', [ContratoInteligenteController::class, 'receberUpload'])->name('contratos.upload.receber');
Route::post('contratos/salvar', [ContratoInteligenteController::class, 'salvar'])->name('contratos.salvar');
Route::post('contratos/extrair', [ContratoController::class, 'extrair'])->name('contratos.extrair');
// Upload de PDF vinculado a um contrato já existente
Route::post('contratos/{contrato}/pdf', [ContratoController::class, 'uploadPdf'])
    ->middleware('can.action:contratos.anexar_documento')
    ->name('contratos.pdf.upload');

// Final Rotas contratos
// Rotas de notificação
Route::middleware(['auth'])->group(function () {
    Route::get('/notificacoes', [NotificationController::class, 'index'])
        ->name('notificacoes.index');

    Route::post('/notificacoes/{notificacao}/lida', [NotificationController::class, 'marcarLida'])
        ->name('notificacoes.lida');

    Route::post('/notificacoes/marcar-todas', [NotificationController::class, 'marcarTodas'])
        ->name('notificacoes.todas');

    // Dados em JSON para DataTables
    Route::get('/notificacoes/data', [NotificationController::class, 'data'])
        ->name('notificacoes.data');

    // Envia uma notificação de teste para o usuário autenticado
    Route::post('/notificacoes/teste', [NotificationController::class, 'teste'])
        ->name('notificacoes.teste');

    Route::post('/push/subscribe', function (\Illuminate\Http\Request $request) {
        auth()->user()->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth']
        );

        return ['success' => true];
    })->name('push.subscribe');
});
// final das rotas de notificação

// ROTAS - AJAX
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
        'series' => array_reverse($series),
    ]);
});
Route::get('/monitoramentos/heatmap', function () {
    $hosts = \App\Models\Host::with(['escola.dre', 'monitoramentos' => function ($q) {
        $q->orderByDesc('ultima_verificacao')->limit(1);
    }])->get();

    $pontos = $hosts->filter(fn ($h) => $h->escola && $h->escola->latitude && $h->escola->longitude)
        ->map(function ($h) {
            $m = $h->monitoramentos->first();

            return [
                'host_id' => $h->id,
                'nome' => $h->nome_conexao,
                'dre' => $h->escola->dre->nome ?? null,
                'escola' => $h->escola->nome ?? null,
                'lat' => (float) $h->escola->latitude,
                'lng' => (float) $h->escola->longitude,
                'online' => $m?->online ?? 0,
                'latencia' => $m?->latencia,
            ];
        })
        ->values();

    return response()->json($pontos);
})->name('api.monitoramentos.heatmap');

Route::get('/monitoramentos/mikrotik/{host}', function (\App\Models\Host $host) {
    $logs = $host->monitoramentos()
        ->orderByDesc('ultima_verificacao')
        ->limit(50)
        ->get(['ultima_verificacao', 'rx_rate', 'tx_rate'])
        ->sortBy('ultima_verificacao'); // reordena asc p/ gráfico

    return response()->json([
        'labels' => $logs->pluck('ultima_verificacao')->map(fn ($d) => $d->format('H:i'))->values(),
        'rx' => $logs->pluck('rx_rate')->values(),
        'tx' => $logs->pluck('tx_rate')->values(),
    ]);
})->name('api.monitoramentos.mikrotik');
Route::get('/api/monitoramentos/heatline', [MonitoramentoController::class, 'apiHeatline'])
    ->name('api.monitoramentos.heatline');
Route::get('/api/monitoramentos/matrix', [MonitoramentoController::class, 'apiMatrix'])
    ->name('api.monitoramentos.matrix');
if (class_exists(\App\Http\Controllers\Api\NocMapController::class)) {
    Route::get('/api/noc/mapa-sla', [\App\Http\Controllers\Api\NocMapController::class, 'mapaSla'])
        ->name('api.noc.mapa-sla');
}

if (class_exists(\App\Http\Controllers\Api\NocStatsController::class)) {
    Route::get('/api/noc/top-downtime', [\App\Http\Controllers\Api\NocStatsController::class, 'topDowntime'])
        ->name('api.noc.top-downtime');
}

Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('apf', [ProjetoApiController::class, 'apf'])->name('api.projetos.apf');
    Route::get('atividades', [ProjetoApiController::class, 'atividades'])->name('api.projetos.atividades');
    Route::get('medicao', [ProjetoApiController::class, 'medicao'])->name('api.projetos.medicao');
    Route::get('boletins', [ProjetoApiController::class, 'boletins'])->name('api.projetos.boletins');

    // Dashboard
    Route::get('dashboard/pf-ust', [ProjetoApiController::class, 'dashboardPfUst'])->name('api.projetos.dashboard.pf_ust');
    Route::get('dashboard/esforco', [ProjetoApiController::class, 'dashboardEsforco'])->name('api.projetos.dashboard.esforco');
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
// JasperReports demo
// Removido demo JasperReports

Route::middleware(['auth'])->group(function () {

    // Permitir acesso ao dashboard IA para Admin, Gestor e Fiscais
    Route::middleware(['role:Administrador,Gestor de Contrato,admin,gestor,Fiscal,fiscal_administrativo,fiscal_tecnico'])->group(function () {
        Route::get('/ia-dashboard', function () {
            return view('fiscalizer-ia');
        })->name('fiscalizer-ia.index');
    });

    // Ordens de Fornecimento
    Route::middleware(['role:Administrador,Gestor de Contrato,Fiscal'])
        ->prefix('ordens-fornecimento')
        ->name('ordens_fornecimento.')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\OrdemFornecimentoController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\OrdemFornecimentoController::class, 'show'])->name('show');
            Route::get('/{id}/pdf', [\App\Http\Controllers\OrdemFornecimentoController::class, 'pdf'])->name('pdf');
        });

    // Relatórios: contratos (PDF via DomPDF)
    Route::get('/reports/contratos', [\App\Http\Controllers\JasperReportsController::class, 'contratos'])
        ->name('reports.contratos');

    // Preview do Termo de Referência (layout Blade)
    Route::get('/reports/termo-referencia', [TermoReferenciaController::class, 'preview'])
        ->name('reports.tr.preview');

    // Relatórios por templates (Word/Excel)
    Route::post('/reports/templates/docx', [\App\Http\Controllers\TemplateReportsController::class, 'generateDocx'])
        ->name('reports.templates.docx');
    Route::post('/reports/templates/xlsx', [\App\Http\Controllers\TemplateReportsController::class, 'generateXlsx'])
        ->name('reports.templates.xlsx');

});
// Admin: Notificações (eventos/templates)
Route::middleware(['auth', 'can.action:system.admin'])
    ->prefix('admin/notificacoes')
    ->name('admin.notificacoes.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationEventController::class, 'index'])->name('index');
        Route::get('/{evento}', [\App\Http\Controllers\NotificationEventController::class, 'show'])->name('show');
        Route::get('/create', [\App\Http\Controllers\NotificationEventController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\NotificationEventController::class, 'store'])->name('store');
        Route::get('/{evento}/edit', [\App\Http\Controllers\NotificationEventController::class, 'edit'])->name('edit');
        Route::put('/{evento}', [\App\Http\Controllers\NotificationEventController::class, 'update'])->name('update');
        Route::delete('/{evento}', [\App\Http\Controllers\NotificationEventController::class, 'destroy'])->name('destroy');

        Route::post('/import', [\App\Http\Controllers\NotificationEventController::class, 'importFromConfig'])->name('import');
        Route::post('/sync', [\App\Http\Controllers\NotificationEventController::class, 'syncActions'])->name('sync');
        Route::get('/users/search', [\App\Http\Controllers\NotificationEventController::class, 'searchUsers'])->name('users.search');


    });

// Inventário por Unidade (somente perfil regional)
Route::middleware(['auth','can:inventario.unidades.gerenciar'])->group(function () {
    Route::get('/inventario/unidades', [\App\Http\Controllers\InventarioController::class, 'selecionarUnidade'])
        ->name('inventario.unidades.select');
    Route::post('/inventario/dres/{dre}/acessar', [\App\Http\Controllers\InventarioController::class, 'acessarPorDre'])
        ->name('inventario.dres.acessar');
    Route::get('/unidades/{unidade}/inventario', [\App\Http\Controllers\InventarioController::class, 'index'])
        ->name('unidades.inventario');
    Route::post('/unidades/{unidade}/inventario', [\App\Http\Controllers\InventarioController::class, 'store'])
        ->name('unidades.inventario.store');
    Route::post('/equipamentos/{equipamento}/quebra', [\App\Http\Controllers\InventarioController::class, 'reportarQuebra'])
        ->name('equipamentos.quebra');
    Route::post('/unidades/{unidade}/reposicoes', [\App\Http\Controllers\InventarioController::class, 'solicitarReposicao'])
        ->name('unidades.reposicoes.solicitar');
    Route::post('/unidades/{unidade}/conexoes', [\App\Http\Controllers\InventarioController::class, 'storeConexao'])
        ->name('unidades.conexoes.store');
    Route::get('/unidades/{unidade}/especificacoes', [\App\Http\Controllers\InventarioController::class, 'gerarEspecificacoes'])
        ->name('unidades.especificacoes');
});

// Monitoramento dos Agentes (inventário)
Route::get('/inventario/monitoramento', [\App\Http\Controllers\TelemetriaDashboardController::class, 'index'])
    ->middleware(['auth','can:ver-inventario'])
    ->name('inventario.monitoramento');
Route::middleware(['auth'])->group(function () {
    Route::post('unidades/{unidade}/normas/upload', [InvController::class, 'uploadNorma'])
        ->name('unidades.normas.upload');

    Route::post('ocorrencias/{ocorrencia}/cit/receber', [InvController::class, 'citReceberOcorrencia'])
        ->middleware(['auth','role:CIT,Administrador'])
        ->name('ocorrencias.cit.receber');
    Route::post('ocorrencias/{ocorrencia}/cit/avaliar', [InvController::class, 'citAvaliarOcorrencia'])
        ->middleware(['auth','role:CIT,Administrador'])
        ->name('ocorrencias.cit.avaliar');

    Route::post('reposicoes/{reposicao}/detec/aprovar', [InvController::class, 'detecAprovarReposicao'])
        ->middleware(['auth','role:DETEC,Administrador'])
        ->name('reposicoes.detec.aprovar');
    Route::post('reposicoes/{reposicao}/detec/entregar', [InvController::class, 'detecRegistrarEntrega'])
        ->middleware(['auth','role:DETEC,Administrador'])
        ->name('reposicoes.detec.entregar');
    Route::post('reposicoes/{reposicao}/detec/baixar/{equipamento}', [InvController::class, 'detecBaixarEquipamento'])
        ->middleware(['auth','role:DETEC,Administrador'])
        ->name('reposicoes.detec.baixar');
});
// Scrap CSV
Route::get('scrap/test', [\App\Http\Controllers\ScrapController::class, 'index'])->name('scrap.test');
Route::post('scrap/fetch', [\App\Http\Controllers\ScrapController::class, 'fetch'])->name('scrap.fetch');
Route::post('scrap/swagger', [\App\Http\Controllers\ScrapController::class, 'fetchSwagger'])->name('scrap.swagger');
