<?php

use App\Http\Controllers\MapaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EscolaController;
use App\Http\Controllers\ContratoController;
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

use App\Models\User;
use App\Models\Role;

// Página inicial (redireciona para login ou dashboard)

Route::get('/', function () {
    return view('site.index');
});



    // Home visível a qualquer usuário autenticado
    Route::resource('home', DashboardController::class);

// Alias para evitar o erro "Route [home] not defined"
Route::get('/home', [DashboardController::class, 'index'])->name('home');
Route::resource('empresas', EmpresaController::class);
Route::resource('contratos', ContratoController::class);
Route::resource('medicoes', MedicaoController::class);
Route::resource('funcoes-sistema', FuncaoSistemaController::class);
Route::resource('documentos', DocumentoController::class);
Route::resource('ocorrencias-fiscalizacao', OcorrenciaFiscalizacaoController::class);
Route::resource('ocorrencias', OcorrenciaController::class);
Route::resource('monitoramentos', MonitoramentoController::class);
Route::resource('projetos', ProjetoController::class);

Route::get('/escolas-data', [App\Http\Controllers\EscolaController::class, 'getData'])->name('escolas.data');

Route::resource('escolas', EscolaController::class);



// Rotas para o mapa


Route::get('/mapa', [MapaController::class, 'index'])->name('mapa');
Route::get('/api/escolas', [MapaController::class, 'escolasGeoJson'])->name('api.escolas');



Route::resource('dres', DREController::class);
Route::get('relatorios/gerar', [RelatorioController::class, 'gerar'])->name('relatorios.gerar');
Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');

// Rotas de autenticação
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
Route::get('password/reset', [ResetPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail '])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Rotas protegidas por middleware de autenticação
Route::middleware(['auth'])->group(function () {
    // Rotas protegidas aqui
});
// Página de acesso negado
Route::get('/403', function () {
    return view('errors.403');
})->name('403');
// Página de não encontrado
Route::get('/404', function () {
    return view('errors.404');
})->name('404');
