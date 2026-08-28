<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePasswordMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // If user has not accepted terms or has pending invitation status, redirect to force password page
            if (($user->terms_accepted_at === null || $user->status === 'pending_invite') && !$request->routeIs('password.force_change', 'password.force_change.post', 'logout')) {
                return redirect()->route('password.force_change');
            }
        }

        return $next($request);
    }
}
