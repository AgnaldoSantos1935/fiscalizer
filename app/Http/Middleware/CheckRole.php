<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Converte os roles de string para inteiros
        $roleIds = array_map('intval', $roles);

        if (!$user || !$user->role_id || !in_array($user->role_id, $roleIds)) {
            //abort(403, 'Acesso negado.');
            return redirect()->route('acesso.negado'); //  Redireciona para a rota de acesso negado
        }

        return $next($request);
    }
}
