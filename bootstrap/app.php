<?php

use App\Http\Middleware\CanAction;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias personalizados (acessíveis em routes/web.php)
        $middleware->alias([
            'role' => CheckRole::class,
            'can.action' => CanAction::class,
        ]);

        // Middleware globais (se quiser aplicar a todos os requests)
        // $middleware->append(\App\Http\Middleware\SomeGlobalMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /**
         * Tratamento customizado de exceções
         */
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // Página de acesso negado (403)
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [], 403);
            }

            // Página não encontrada (404)
            if ($e->getStatusCode() === 404) {
                return response()->view('errors.404', [], 404);
            }

            return null;
        });
    })
    ->create();
