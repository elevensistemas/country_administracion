<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * Display a listing of system audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('auditable_type', 'like', "%{$search}%")
                  ->orWhere('old_values', 'like', "%{$search}%")
                  ->orWhere('new_values', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }
}
