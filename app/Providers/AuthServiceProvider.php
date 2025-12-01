<?php

namespace App\Providers;

use App\Models\Action;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
            // Admin (role_id=1) tem acesso total
            if ($user && $user->role_id === 1) {
                \Illuminate\Support\Facades\Log::debug('Gate::before - Admin concede: ' . $ability);

                return true;
            }

            // Fallback RBAC: concede se o usuário possui a Action (suporta curingas)
            if ($user && method_exists($user, 'hasAction') && $user->hasAction($ability)) {
                \Illuminate\Support\Facades\Log::debug('Gate::before - RBAC concede por hasAction("' . $ability . '")');

                return true;
            }

            // Sem decisão aqui; permite que outros gates/policies decidam
            return null;
        });

        Gate::define('manage-contrato', function ($user, \App\Models\Contrato $contrato) {
            return $contrato->usuarioVinculado($user);
        });

        // Permite múltiplos papéis (IDs: 1=Administrador, 2=Gestor de Contrato, 3=Fiscal)
        /*  Gate::define('view-contratos', fn($user) => in_array($user->role_id, [1, 2, 3]));
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
          Gate::define('view-index-host', fn($user) => in_array($user->role_id, [1, 2, 3]));
          Gate::define('view-testar_host-monitoramentos', fn($user) => in_array($user->role_id, [1, 2, 3]));
          Gate::define('view-create-host', fn($user) => in_array($user->role_id, [1, 2, 3]));
          Gate::define('view-index-monitoramento', fn($user) => in_array($user->role_id, [1, 2, 3]));
          Gate::define('view-index-host_testes', fn($user) => in_array($user->role_id, [1, 2, 3]));
          Gate::define('view-dashboard-host_testes', fn($user) => in_array($user->role_id, [1, 2, 3]));
          Gate::define('view-historico-host_testes', fn($user) => in_array($user->role_id, [1, 2, 3]));
             Gate::define('view-index-projetos_soft', fn($user) => in_array($user->role_id, [1, 2, 3]));
             Gate::define('view-show-projetos_soft', fn($user) => in_array($user->role_id, [1, 2, 3]));




          Gate::define('view-index-empenhos', fn($user) => in_array($user->role_id, [1, 2, 3]));
           Gate::define('view-create-empenhos', fn($user) => in_array($user->role_id, [1, 2, 3]));
           Gate::define('view-show-empenhos', fn($user) => in_array($user->role_id, [1, 2, 3]));*/
        // Permite papel único (ID 1=Administrador)
        // Permite papel único (ID 1=Administrador)
        /*Gate::define('view-index-empresas', fn($user) => $user->role_id === 2);
        Gate::define('view-create-empresas', fn($user) => $user->role_id === 2);
        Gate::define('view-create-contratos', fn($user) => $user->role_id === 2);
        Gate::define('view-escolas', fn($user) => $user->role_id === 2);
        Gate::define('view-index-user_profiles', fn($user) => $user->role_id === 2);
        Gate::define('view-create-user_profiles', fn($user) => $user->role_id === 2);*/

        // ===== Conceder acesso para Fiscais (Administrativo e Técnico) =====
        $allowFiscal = function ($user): bool {
            $roleName = is_object($user->role)
                ? ($user->role->nome ?? $user->role->name ?? null)
                : ($user->role ?? null);

            return in_array($roleName, ['fiscal_administrativo', 'fiscal_tecnico']);
        };

        // Contratos
        Gate::define('view-contratos', fn ($user) => $allowFiscal($user));

        // Projetos
        Gate::define('view-projetos', fn ($user) => $allowFiscal($user));

        // Medições
        Gate::define('view-medicoes', fn ($user) => $allowFiscal($user));

        // Empenhos
        Gate::define('view-index-empenhos', fn ($user) => $allowFiscal($user));
        Gate::define('view-create-empenhos', fn ($user) => $allowFiscal($user));

        // Conexões (Hosts e Monitoramentos)
        Gate::define('view-index-host', fn ($user) => $allowFiscal($user));
        Gate::define('view-create-host', fn ($user) => $allowFiscal($user));
        Gate::define('view-index-monitoramento', fn ($user) => $allowFiscal($user));

        // Mapas (Escolas)
        Gate::define('view-escolas', fn ($user) => $allowFiscal($user));

        // Projetos de Software
        Gate::define('view-index-projetos_soft', fn ($user) => $allowFiscal($user));
        Gate::define('view-show-projetos_soft', fn ($user) => $allowFiscal($user));

        // Projetos de Software
        Gate::define('view-index-equipamentos', fn ($user) => $allowFiscal($user));

        // Dashboard IA / Base de dados
        Gate::define('view-ia-dashboard', fn ($user) => $allowFiscal($user));

        // Inventário de Unidades (apenas usuários lotados em coordenações regionais)
        Gate::define('inventario.unidades.gerenciar', function ($user) {
            try {
                $profile = \App\Models\UserProfile::where('user_id', $user->id)->first();
                $lotacao = trim(strtolower($profile->lotacao ?? ''));

                return $lotacao === 'coordenação regional' || str_contains($lotacao, 'regional');
            } catch (\Throwable $e) {
                return false;
            }
        });

        // ===== Gates dinâmicos baseados em Actions (RBAC) =====
        // Carrega os códigos de actions e define gates que utilizam User->hasAction()
        try {
            if (Schema::hasTable('actions')) {
                $actions = Cache::remember('rbac_actions_list', 300, function () {
                    return Action::query()->select(['codigo'])->get();
                });

                foreach ($actions as $action) {
                    Gate::define($action->codigo, function ($user) use ($action) {
                        return $user->hasAction($action->codigo);
                    });
                }

                Gate::define('view-index-user_profiles', function ($user) {
                    return $user->role_id === 1;
                });
                Gate::define('view-create-user_profiles', function ($user) {
                    return $user->role_id === 1;
                });
            }
        } catch (\Throwable $e) {

        }

    }
}
