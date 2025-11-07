<?php

use App\Http\Controllers\HostController;


Route::get('/hosts/{id}/historico', [HostController::class, 'historico']);
