<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user || !$user->role || !in_array($user->role->nome, $roles)) {
            //abort(403, 'Acesso negado.');
            return redirect()->route('acesso.negado');
        }

        return $next($request);
    }
}
