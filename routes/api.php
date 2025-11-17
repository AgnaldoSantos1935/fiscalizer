<?php

use App\Http\Controllers\HostController;
use App\Http\Controllers\MonitoramentoController;
use App\Http\Controllers\HostMonitorController;
use App\Http\Controllers\Api\ProjetoApiController;
use App\Http\Controllers\Api\HostApiController;
use App\Http\Controllers\Api\NocMapController;
use App\Http\Controllers\MapaController;
use Illuminate\Support\Facades\Route;

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





Route::get('/hosts/status', [HostApiController::class, 'status']);

Route::get('api/hosts', [HostApiController::class, 'index'])->name('api.hosts');

