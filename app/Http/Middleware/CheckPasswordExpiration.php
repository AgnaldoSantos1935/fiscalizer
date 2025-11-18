<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPasswordExpiration
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // 🔹 Se for senha provisória, redireciona para tela de troca
        if ($user && $user->must_change_password) {
            return redirect()->route('password.force-change')
                ->with('warning', 'Sua senha é provisória. Altere para continuar.');
        }

        // 🔹 Senha expirada já é tratada no LoginController (antes de logar)
        return $next($request);
    }
}
