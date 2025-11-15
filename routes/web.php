<?php

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
use App\Http\Controllers\MedicaoTelcoController;
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
Route::resource('ocorrencias-fiscalizacao', OcorrenciaFiscalizacaoController::class);
Route::resource('ocorrencias', OcorrenciaController::class);
Route::resource('projetos', ProjetoController::class);
Route::resource('user_profiles', UserProfileController::class);
 Route::resource('usuarios',UserProfileController::class);
 Route::resource('empresas', EmpresaController::class);
Route::resource('empenhos', EmpenhoController::class);
Route::resource('hosts', HostController::class);
Route::resource('dres', DREController::class);
Route::resource('host_testes', HostTesteController::class)->only(['index', 'show']);
Route::resource('projetos', ProjetoSoftwareController::class);
Route::resource('projetos.apfs', ApfController::class); // nested: /projetos/{projeto}/apfs
Route::resource('projetos.fiscalizacoes', FiscalizacaoProjetoController::class)->shallow();
Route::resource('pessoas', PessoaController::class);
Route::resource('servidores', ServidorController::class);
Route::resource('boletins', BoletimMedicaoController::class);
Route::resource('demandas', DemandaController::class);

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

//Final Rotas contratos
