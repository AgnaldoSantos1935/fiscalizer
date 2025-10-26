<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


        Gate::define('view-contratos', fn($user) => $user->hasRole(['Administrador','Gestor de Contrato','Fiscal']));
        Gate::define('view-empresas', fn($user) => $user->hasRole('Administrador'));

}
}
