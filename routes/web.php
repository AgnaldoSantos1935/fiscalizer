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

// Rotas de autenticação
Auth::routes(['register' => false, 'reset' => true]);

    // Home visível a qualquer usuário autenticado
    Route::get('home', [DashboardController::class, 'index'])->name('home');

// Alias para evitar o erro "Route [home] not defined"

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

Route::get('/contratos/{id}/itens', [App\Http\Controllers\ContratoController::class, 'itens'])
    ->name('contratos.itens');


Route::get('contratos/{id}/itens', [ContratoController::class, 'getItens'])
    ->name('contratos.itens');



// Rotas para o mapa


Route::get('/mapa/escolas', function () {
    return view('mapas.escolas');

})->name('mapa.escolas');

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
