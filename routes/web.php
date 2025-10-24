<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\MedicaoController;
use App\Http\Controllers\FuncaoSistemaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\OcorrenciaFiscalizacaoController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Redireciona a raiz para a listagem de empresas
Route::get('/', function () {
    return redirect()->route('empresas.index');
});

//  Rotas completas de CRUD para Empresas
Route::resource('empresas', EmpresaController::class)
    ->names('empresas'); // gera: empresas.index, empresas.create, empresas.store, etc.

//  Rotas completas de CRUD para Contratos
Route::resource('contratos', ContratoController::class)
    ->names('contratos'); // gera: contratos.index, contratos.create, contratos.store, etc.

    // Route::get('/', fn() => redirect()->route('contratos.index'));

Route::resource('empresas', EmpresaController::class);
Route::resource('contratos', ContratoController::class);
Route::resource('medicoes', MedicaoController::class);
Route::resource('funcoes', FuncaoSistemaController::class);
Route::resource('documentos', DocumentoController::class);
Route::resource('ocorrencias', OcorrenciaFiscalizacaoController::class);
