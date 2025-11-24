<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CanAction
{
    /**
     * Handle an incoming request.
     * Usage: route middleware 'can.action:action_code'
     */
    public function handle(Request $request, Closure $next, string $actionCode)
    {
        if (! auth()->check()) {
            abort(403);
        }

        // Usa Gate (que já tem fallback via Gate::before -> hasAction)
        if (! Gate::allows($actionCode)) {
            abort(403);
        }

        return $next($request);
    }
}
