<?php

if (! function_exists('user_can')) {
    /**
     * Helper: verifica se o usuário autenticado possui a ação/código informado.
     * Usa Gate::allows, que já tem fallback para User::hasAction().
     */
    function user_can(string $action): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return \Illuminate\Support\Facades\Gate::allows($action);
    }
}

if (! function_exists('notify_event')) {
    /**
     * Helper: dispara notificação para usuários autorizados via RBAC.
     * Código no formato: notificacoes.<dominio>.<evento>
     */
    function notify_event(string $codigo, array $data = [], $subject = null): void
    {
        app(\App\Services\NotificationEventService::class)->notify($codigo, $data, $subject);
    }
}
