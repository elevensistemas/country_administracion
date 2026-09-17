<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Show force change password form.
     */
    public function showForceChangeForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // If already accepted and active, redirect to their home
        if ($user->terms_accepted_at !== null && $user->status !== 'pending_invite') {
            return $user->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('owner.dashboard');
        }

        return view('auth.force-change-password');
    }

    /**
     * Handle force change password request.
     */
    public function forceChange(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $request->validate([
            'password' => ['required', 'confirmed', 'min:6'],
            'terms' => 'required|accepted',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas ingresadas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'terms.required' => 'Debes aceptar los términos y condiciones para continuar.',
            'terms.accepted' => 'Debes aceptar los términos y condiciones para continuar.',
        ]);

        // Update password and accept terms
        $user->password = Hash::make($request->input('password'));
        $user->terms_accepted_at = now();
        if ($user->status === 'pending_invite') {
            $user->status = 'active';
        }
        $user->save();

        session()->flash('success', 'Contraseña configurada con éxito. ¡Bienvenido al sistema!');

        // Redirect based on role
        if ($user->isAdmin() || $user->relationship_type === 'accounting' || $user->relationship_type === 'operator') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('owner.dashboard');
    }

    /**
     * Show recovery email form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle recovery email request with real token generation and dispatch.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', 'Hemos enviado un correo con el enlace para restablecer tu contraseña.')
            : back()->withErrors(['email' => 'No pudimos encontrar un usuario con ese correo electrónico o no fue posible enviar el correo.']);
    }

    /**
     * Show reset password form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Handle reset password request with real token verification.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'token.required' => 'El token de recuperación es inválido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(\Illuminate\Support\Str::random(60));
                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Contraseña restablecida con éxito. Por favor inicia sesión con tu nueva contraseña.')
            : back()->withErrors(['email' => 'El enlace de recuperación es inválido o ha expirado. Por favor solicita uno nuevo.']);
    }
}
