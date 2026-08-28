<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    /**
     * Update user theme preference.
     */
    public function updateTheme(Request $request)
    {
        if ($request->has('active_lot_id')) {
            $lotId = $request->input('active_lot_id');
            if (Auth::check()) {
                $user = Auth::user();
                $hasLot = $user->functionalUnits()->whereHas('lot', function($q) use ($lotId) {
                    $q->where('id', $lotId);
                })->exists();
                if ($hasLot) {
                    session(['active_lot_id' => $lotId]);
                }
            }
            return response()->json(['status' => 'success', 'active_lot_id' => $lotId]);
        }

        $request->validate([
            'theme' => 'required|string|in:light,dark,auto',
        ]);

        $theme = $request->input('theme');
        session(['theme' => $theme]);

        if (Auth::check()) {
            $user = Auth::user();
            
            $pref = UserPreference::updateOrCreate(
                ['user_id' => $user->id],
                ['theme' => $theme]
            );
        }

        return response()->json([
            'status' => 'success',
            'theme' => $theme,
        ]);
    }
}
