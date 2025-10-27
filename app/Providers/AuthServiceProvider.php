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
         //  Administradores podem tudo
    Gate::before(function ($user, $ability) {
    return true; // temporário, deve mostrar tudo
});

        // Permite múltiplos papéis
        Gate::define('view-contratos', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));
         Gate::define('view-dashboard', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));

          Gate::define('view-monitoramentos', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));

          Gate::define('view-documentos', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));

           Gate::define('view-ocorrencias', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));
           Gate::define('view-funcoes', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));
           Gate::define('view-projetos', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));
           Gate::define('view-medicoes', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));
           Gate::define('view-relatorios', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));
         Gate::define('view-relatorios', fn($user) => in_array($user->role->nome, ['Administrador', 'Gestor de Contrato',
         'Fiscal']));
         // Permite papel único
        Gate::define('view-empresas', fn($user) => $user->hasRole('Administrador'));
        Gate::define('view-pswreset', fn($user) => $user->hasRole('Administrador'));
        Gate::define('view-usrregister', fn($user) => $user->hasRole('Administrador'));


}
}
