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



// Rota API para DataTables / AJAX
Route::get('/api/hosts', [HostController::class, 'getHostsJson'])->name('api.hosts');
Route::get('/api/contratos/{id}/itens', [HostController::class, 'getItensPorContrato']);
Route::get('empenhos/data', [EmpenhoController::class, 'getData'])->name('empenhos.data');
Route::get('/escolas-data', [EscolaController::class, 'getData'])->name('escolas.data');
Route::get('/hosts/dashboard/data', [HostDashboardController::class, 'dadosAjax'])
    ->name('hosts.dashboard.data');
Route::get('/host_testes/historico', [App\Http\Controllers\HostDashboardController::class, 'historicoAjax'])
->name('host_testes.historico');
Route::get('/api/contratos', [App\Http\Controllers\ContratoController::class, 'getContratosJson'])
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

Route::get('relatorios/gerar', [RelatorioController::class, 'gerar'])->name('relatorios.gerar');
Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
//BOLETIM DE MEDIÇÃO
Route::get('boletins/{id}/pdf', [BoletimMedicaoController::class, 'exportPdf'])->name('boletins.pdf');

//FIM DE BOLETIM DE MEDIÇÃO
// DASHBOARD PROJETOS
Route::get('/dashboard/projetos', [DashboardController::class, 'index'])->name('dashboard.projetos');
//FINAL DE DASHBOARD PROJETOS
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

// FINA LDA ROTA DE REQUISITOS
