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
        // Administradores podem tudo (ID 1)
        Gate::before(function ($user, $ability) {
            if ($user && $user->role_id === 1) { // ID do Administrador
                \Illuminate\Support\Facades\Log::debug("Gate::before - Usuário é Administrador (ID 1), concedendo permissão para: " . $ability);
                return true;
            }
            \Illuminate\Support\Facades\Log::debug("Gate::before - Verificação falhou para: " . $ability . " - User ID: " . ($user ? $user->id : 'null') . " - Role ID: " . ($user ? $user->role_id : 'null'));
            return null;
        });

        // Permite múltiplos papéis (IDs: 1=Administrador, 2=Gestor de Contrato, 3=Fiscal)
        Gate::define('view-contratos', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-mapa1', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-dashboard', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-monitoramentos', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-documentos', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-ocorrencias', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-funcoes', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-projetos', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-medicoes', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-relatorios', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-usrregister', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-pswreset', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-create-escola', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-index-dre', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-create-dre', fn($user) => in_array($user->role_id, [1, 2, 3]));
        Gate::define('view-create-empenho', fn($user) => in_array($user->role_id, [1, 2, 3]));
        // Permite papel único (ID 1=Administrador)
        // Permite papel único (ID 1=Administrador)
        Gate::define('view-index-empresas', fn($user) => $user->role_id === 2);
        Gate::define('view-create-empresas', fn($user) => $user->role_id === 2);
        Gate::define('view-escolas', fn($user) => $user->role_id === 2);

}
}
