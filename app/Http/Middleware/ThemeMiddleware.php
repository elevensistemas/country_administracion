<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ThemeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $theme = 'auto';

        if (Auth::check()) {
            $user = Auth::user();
            $pref = $user->preferences;
            
            if ($pref) {
                $theme = $pref->theme;
            }
        } else {
            $theme = session('theme', 'auto');
        }

        // Share the theme with all views
        View::share('currentTheme', $theme);

        return $next($request);
    }
}
