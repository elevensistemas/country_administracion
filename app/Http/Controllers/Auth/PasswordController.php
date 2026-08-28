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
     * Handle recovery email request (simulated for simplicity/stability).
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // Logically we'd send email here. Let's just flash success and redirect back.
        return back()->with('status', 'Hemos enviado un correo para restablecer tu contraseña. (Simulado)');
    }

    /**
     * Show reset password form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Handle reset password request.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Simulated password reset
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->route('login')->with('success', 'Contraseña restablecida con éxito.');
        }

        return back()->withErrors(['email' => 'No pudimos encontrar un usuario con ese correo electrónico.']);
    }
}
