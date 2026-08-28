<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdoptionController extends Controller
{
    /**
     * Display usage metrics, login history, and adoption stats.
     */
    public function index(Request $request)
    {
        // 1. Adoption Metrics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $pendingInvites = User::where('status', 'pending_invite')->count();
        $blockedUsers = User::where('status', 'blocked')->count();

        $adoptionRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;

        // 2. Login Logs (Devices, IP, timestamps)
        $query = LoginLog::with('user');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                   ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhere('ip_address', 'like', "%{$search}%")
              ->orWhere('user_agent', 'like', "%{$search}%");
        }

        $loginLogs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // 3. Top IPs
        $topIps = LoginLog::select('ip_address', DB::raw('count(*) as count'))
            ->groupBy('ip_address')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        return view('admin.adoption.index', compact(
            'totalUsers', 'activeUsers', 'pendingInvites', 'blockedUsers', 
            'adoptionRate', 'loginLogs', 'topIps'
        ));
    }
}
