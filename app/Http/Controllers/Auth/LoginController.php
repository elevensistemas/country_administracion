<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin() || $user->relationship_type === 'accounting' || $user->relationship_type === 'operator') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('owner.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Log failed/blocked attempt
            LoginLog::create([
                'user_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'blocked',
                'failed_attempts' => 1,
            ]);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            // Log failed attempt
            LoginLog::create([
                'user_id' => $user ? $user->id : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'failed',
                'failed_attempts' => 1,
            ]);

            throw ValidationException::withMessages([
                'email' => 'Las credenciales provistas son incorrectas.',
            ]);
        }

        if ($user->status === 'blocked' || $user->status === 'inactive') {
            // Log failed attempt due to status
            LoginLog::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'failed',
                'failed_attempts' => 1,
            ]);

            throw ValidationException::withMessages([
                'email' => 'Tu usuario se encuentra bloqueado o inactivo. Contacta a la administración.',
            ]);
        }

        // Login success
        Auth::login($user, $request->boolean('remember'));
        RateLimiter::clear($throttleKey);

        // Update login stats
        $now = now();
        $user->login_count++;
        if ($user->first_login_at === null) {
            $user->first_login_at = $now;
        }
        $user->last_login_at = $now;
        $user->last_login_ip = $request->ip();
        $user->last_login_agent = $request->userAgent();
        
        // If status was pending invite and they logged in, they are now active
        if ($user->status === 'pending_invite' && $user->terms_accepted_at !== null) {
            $user->status = 'active';
        }
        $user->save();

        // Create User Preference if not exists
        if (!$user->preferences()->exists()) {
            UserPreference::create([
                'user_id' => $user->id,
                'theme' => 'auto',
                'notifications_email' => true,
                'notifications_whatsapp' => true,
            ]);
        }

        // Log successful login
        LoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success',
            'failed_attempts' => 0,
        ]);

        $request->session()->regenerate();

        // Redirect based on role
        if ($user->isAdmin() || $user->relationship_type === 'accounting' || $user->relationship_type === 'operator') {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        return redirect()->intended(route('owner.dashboard'));
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
