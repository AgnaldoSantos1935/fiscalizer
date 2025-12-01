<?php

use App\Http\Controllers\Api\ContratoImportController;
use App\Http\Controllers\Api\HostApiController;
use Illuminate\Support\Facades\Route;

Route::get('/hosts/status', [HostApiController::class, 'status']);

Route::get('api/hosts', [HostApiController::class, 'index'])->name('api.hosts');

Route::post('/contratos/import', [ContratoImportController::class, 'import']);
