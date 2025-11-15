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



Route::post('/monitoramentos/update', [MonitoramentoController::class, 'atualizar']);
Route::get('/monitoramentos/historico/{id}', [MonitoramentoController::class, 'historico']);


// Hosts que o Python deve monitorar
Route::get('/hosts-monitor', [HostMonitorController::class, 'listarHosts']);

// Python envia os resultados
Route::post('/monitoramentos/update', [MonitoramentoController::class, 'atualizar']);

// Histórico de um host, usado pelos gráficos
Route::get('/monitoramentos/historico/{id}', [MonitoramentoController::class, 'historico']);

Route::get('/hosts/status', [HostApiController::class, 'status']);

Route::get('api/hosts', [HostApiController::class, 'index'])->name('api.hosts');
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
Route::get('/api/hosts', [HostController::class, 'getHostsJson'])->name('api.hosts');
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
