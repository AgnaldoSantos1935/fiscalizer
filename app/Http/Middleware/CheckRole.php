<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verifica se o usuário autenticado possui um dos papéis exigidos.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // se não houver usuário autenticado ou sem papel definido
        if (!$user || !method_exists($user, 'role') && !isset($user->role)) {
            abort(403, 'Acesso negado.');
        }

        // obtém o nome do papel
        $roleName = is_object($user->role)
            ? ($user->role->nome ?? $user->role->name ?? null)
            : $user->role;

        // se o papel não estiver na lista permitida
        if (!$roleName || !in_array($roleName, $roles)) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
