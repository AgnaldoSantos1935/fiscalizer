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

// Página inicial (redireciona para login ou dashboard)

Route::get('/', function () {
    return view('site.index');
});


Route::get('/ajax/contratos/{id}', [ContratoController::class, 'getContratoJson'])
    ->withoutMiddleware(['auth'])
    ->name('ajax.contrato');


// Rotas de autenticação
Auth::routes(['register' => false, 'reset' => true]);

Route::middleware(['auth', 'password.expiration'])->group(function() {
    Route::resource('user_profiles', UserProfileController::class);
});


    // Home visível a qualquer usuário autenticado
    Route::get('home', [DashboardController::class, 'index'])->name('home');

// Alias para evitar o erro "Route [home] not defined"

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



// 🔹 Rotas testes de rede
// 🔹 Rotas de testes de conexão (pings manuais, diagnóstico)
//Route::get('/teste-conexao', [App\Http\Controllers\HostController::class, 'index'])->name('teste_conexao.index');
//Route::post('/teste-conexao', [App\Http\Controllers\TesteConexaoController::class, 'testar'])->name('teste_conexao.testar');

// 🔹 Rotas de monitoramento automático (CRUD + histórico + teste)
//Route::resource('monitoramentos', MonitoramentoController::class)->except(['show']);
//Route::get('monitoramentos/{id}/testar', [MonitoramentoController::class, 'testar'])->name('monitoramentos.testar');
//Route::get('monitoramentos/{id}/historico', [MonitoramentoController::class, 'historico'])->name('monitoramentos.historico');



// 🔹 Rotas RESTful (CRUD completo)
Route::resource('escolas', EscolaController::class);



// Rotas para empenhos (CRUD)
Route::resource('empenhos', EmpenhoController::class);
Route::get('empenho/{id}/imprimir', [EmpenhoController::class, 'imprimir'])
    ->name('empenho.imprimir');

// CADASTRO DE PERFIS DE USUÁRIOS
Route::get('user_profiles/index', [App\Http\Controllers\UserProfileController::class, 'index'])
->name('user_profiles.index');
Route::get('user_profiles/create', [App\Http\Controllers\UserProfileController::class, 'create'])
->name('user_profiles.create');
Route::get('user_profiles/show', [App\Http\Controllers\UserProfileController::class, 'show'])
->name('user_profiles.show');

// FISCALIZAÇÃO PROJETO DE SOFTWARE
Route::resource('projetos', ProjetoSoftwareController::class);
Route::resource('projetos.apfs', ApfController::class); // nested: /projetos/{projeto}/apfs
Route::resource('projetos.fiscalizacoes', FiscalizacaoProjetoController::class)->shallow();
Route::post('fiscalizacoes/{fiscalizacao}/documentos', [DocumentoProjetoController::class,'store'])->name('fiscalizacoes.documentos.store');

// FINAL DE CADASTRO DE PERFIS DE USUÁRIOS

// Rota API para DataTables / AJAX

    Route::get('empenhos/data', [EmpenhoController::class, 'getData'])->name('empenhos.data');
    Route::resource('empenhos', EmpenhoController::class);

// 🔹 Rota Ajax específica para DataTables
Route::get('/escolas-data', [EscolaController::class, 'getData'])->name('escolas.data');

// 🔹 Rota auxiliar (JSON de detalhes para modal)
Route::get('/escolas/{id}/detalhes', [EscolaController::class, 'detalhes'])
    ->name('escolas.detalhes');

// TESTES E MONITORAMENTO DE CONEXÕES
Route::get('/api/hosts', [HostController::class, 'getHostsJson'])->name('api.hosts');
Route::get('/api/contratos/{id}/itens', [HostController::class, 'getItensPorContrato']);
Route::get('/hosts/{id}', [HostController::class, 'show'])->name('api.hosts.show');
Route::get('/hosts.index', [HostController::class, 'index'])->name('hosts.index');
Route::get('/monitoramentos', [App\Http\Controllers\MonitoramentoController::class, 'index'])
->name('monitoramentos.index');
Route::get('/host_testes/dashboard', [App\Http\Controllers\HostDashboardController::class, 'index'])
->name('host_testes.dashboard');
Route::get('/host_testes/historico', [App\Http\Controllers\HostDashboardController::class, 'historicoAjax'])
->name('host_testes.historico');
// Endpoint AJAX (retorna JSON com dados dos gráficos)
Route::get('/hosts/dashboard/data', [HostDashboardController::class, 'dadosAjax'])
    ->name('hosts.dashboard.data');
    // Endpoint AJAX de histórico detalhado (por host)



// 🔹 CRUD principal de conexões
Route::resource('hosts', HostController::class);

// 🔹 Execução de teste manual (ping individual)
Route::post('/hosts/{id}/testar', [HostTesteController::class, 'executarTesteManual'])
    ->name('hosts.testar');

// 🔹 API auxiliar para selects dinâmicos (Contratos → Itens)
Route::get('/api/contratos/{id}/itens', [HostController::class, 'getItensPorContrato'])
    ->name('api.contratos.itens');

// 🔹 Histórico de testes e resultados detalhados
Route::resource('host_testes', HostTesteController::class)->only(['index', 'show']);



Route::get('contratos/{id}/itens', [ContratoController::class, 'getItens'])
    ->name('contratos.itens');
Route::get('/api/contratos', [App\Http\Controllers\ContratoController::class, 'getContratosJson'])
    ->name('api.contratos');
Route::get('/api/contratos/detalhes/{id}', [App\Http\Controllers\ContratoController::class, 'detalhesContrato'])
    ->name('api.contratos.detalhes');

Route::get('/api/situacoes', [App\Http\Controllers\SituacaoContratoController::class, 'listar'])
    ->name('api.situacoes');



// Rotas para o mapa

Route::get('/mapas/escolas', [MapaController::class, 'index'])->name('mapas.escolas');
Route::get('/api/escolas', [MapaController::class, 'escolasGeoJson'])->name('api.escolas');



Route::resource('dres', DREController::class);
Route::get('relatorios/gerar', [RelatorioController::class, 'gerar'])->name('relatorios.gerar');
Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');

Route::middleware(['auth', 'role:Administrador,Fiscal'])->group(function () {
    Route::resource('empresas', EmpresaController::class);
});
Route::middleware(['auth', 'role:Administrador'])->group(function () {
    Route::resource('usuarios', App\Http\Controllers\UsuarioController::class);
});
