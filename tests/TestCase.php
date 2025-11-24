<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application instance for testing.
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Garante que as rotas não estejam em cache durante os testes
        try {
            \Artisan::call('route:clear');
        } catch (\Throwable $e) {
            // Ignora qualquer falha ao limpar cache de rotas em ambientes de teste
        }

        // As rotas já são carregadas via Application::configure()->withRouting em bootstrap/app.php
        // Evitamos carregá-las novamente para prevenir conflitos de registro duplicado

        return $app;
    }
}
