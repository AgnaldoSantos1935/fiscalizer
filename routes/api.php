<?php

use App\Http\Controllers\Api\HostApiController;
use Illuminate\Support\Facades\Route;

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
        'series' => array_reverse($series),
    ]);
});
