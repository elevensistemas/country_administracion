<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role) || $user->relationship_type === $role) {
                return $next($request);
            }
        }

        // Graceful redirect based on user relationship type (simil app, never show raw 403)
        if ($user->isAdmin() || in_array($user->relationship_type, ['admin', 'superadmin', 'operator', 'accounting'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('owner.dashboard');
    }
}
