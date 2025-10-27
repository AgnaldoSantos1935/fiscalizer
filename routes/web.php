<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\MedicaoController;
use App\Http\Controllers\FuncaoSistemaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\OcorrenciaFiscalizacaoController;
use App\Http\Controllers\MonitoramentoController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\RelatorioController;

// Página inicial (redireciona para login ou dashboard)

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

// 🔐 Login e Logout
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/password/reset', [ResetPasswordController::class, 'showResetForm'])->name('password.request');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
    Route::resource('relatorios', RelatorioController::class);
    Route::get('relatorios-export/excel', [RelatorioController::class, 'exportExcel'])->name('relatorios.export.excel');
    Route::get('relatorios-export/pdf', [RelatorioController::class, 'exportPdf'])->name('relatorios.export.pdf');

    // Home visível a qualquer usuário autenticado
    Route::resource('home', HomeController::class);

    //  Monitoramentos acessíveis a todos os papéis
    Route::middleware('role:Administrador,Gestor de Contrato,Fiscal,Consulta')
        ->resource('monitoramentos', MonitoramentoController::class);

    //  Acesso total - Administrador
    Route::middleware('role:Administrador')->group(function () {
        Route::resources([
            'empresas'      => EmpresaController::class,
            'contratos'     => ContratoController::class,
            'medicoes'      => MedicaoController::class,
            'funcoes'       => FuncaoSistemaController::class,
            'documentos'    => DocumentoController::class,
            'ocorrencias'   => OcorrenciaFiscalizacaoController::class,
        ]);
    });

    // ⚙️ Acesso intermediário - Gestor e Fiscal
    Route::middleware('role:Gestor de Contrato,Fiscal')->group(function () {
        Route::resources([
            'empresas'      => EmpresaController::class,
            'projetos'      => ProjetoController::class,
            'ocorrencias'   => OcorrenciaFiscalizacaoController::class,
            'documentos'    => DocumentoController::class,
            'contratos'     => ContratoController::class,
            'medicoes'      => MedicaoController::class,
            'funcoes'       => FuncaoSistemaController::class,
            'monitoramentos' => MonitoramentoController::class,
            'relatorios'    => RelatorioController::class,
        ]);
    });

    // 👁️ Acesso de consulta (leitura)
    Route::middleware('role:Consulta')->group(function () {
        Route::resource('monitoramentos', MonitoramentoController::class)->only(['index', 'show']);
    });
});

Route::get('/acesso-negado', function () {
    return view('errors.acesso_negado');
})->name('acesso.negado');
/*
Route::resource('/dashboard', DashboardController::class);
Route::resource('/home', HomeController::class);
Route::resource('/empresas', MonitoramentoController::class);*/
