<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Owner;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display owner profile information.
     */
    public function show()
    {
        $user = auth()->user();
        
        // Find owner profile associated to this user email/phone
        $owner = Owner::where('email', $user->email)->first();

        return view('owner.profile.show', compact('user', 'owner'));
    }

    /**
     * Update owner profile preference.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // Also update the Owner profile if it exists
        $owner = Owner::where('email', $user->email)->first();
        if ($owner) {
            $owner->update([
                'phone' => $request->phone,
            ]);
        }

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
