<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditController extends Controller
{
    /**
     * Display a listing of system audit logs and login logs.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'audits'); // 'audits' or 'logins'

        // 1. Metrics for header cards
        $todayStart = Carbon::today();
        $metrics = [
            'total_audits'     => AuditLog::count(),
            'today_audits'     => AuditLog::where('created_at', '>=', $todayStart)->count(),
            'today_logins'     => LoginLog::where('created_at', '>=', $todayStart)->where('status', 'success')->count(),
            'today_failed_log' => LoginLog::where('created_at', '>=', $todayStart)->where('status', '!=', 'success')->count(),
        ];

        // 2. Dropdown options
        $users = User::orderBy('name')->get(['id', 'name', 'last_name', 'email']);
        $modelOptions = AuditLog::getModelNamesMap();

        // 3. Tab: AUDITS (Cambios y Cargas en Modelos)
        $auditQuery = AuditLog::with('user');

        if ($request->filled('action')) {
            $auditQuery->where('action', $request->input('action'));
        }

        if ($request->filled('model_type')) {
            $auditQuery->where('model_type', $request->input('model_type'));
        }

        if ($request->filled('user_id')) {
            $auditQuery->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('date_from')) {
            $auditQuery->where('created_at', '>=', Carbon::parse($request->input('date_from'))->startOfDay());
        }

        if ($request->filled('date_to')) {
            $auditQuery->where('created_at', '<=', Carbon::parse($request->input('date_to'))->endOfDay());
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $auditQuery->where(function ($q) use ($search) {
                $q->where('model_type', 'like', "%{$search}%")
                  ->orWhere('old_values', 'like', "%{$search}%")
                  ->orWhere('new_values', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $auditLogs = $auditQuery->orderByDesc('created_at')->paginate(20, ['*'], 'audit_page')->withQueryString();

        // 4. Tab: LOGINS (Inicios de sesión / Accesos)
        $loginQuery = LoginLog::with('user');

        if ($request->filled('login_status')) {
            $loginQuery->where('status', $request->input('login_status'));
        }

        if ($request->filled('login_user_id')) {
            $loginQuery->where('user_id', $request->input('login_user_id'));
        }

        if ($request->filled('login_date_from')) {
            $loginQuery->where('created_at', '>=', Carbon::parse($request->input('login_date_from'))->startOfDay());
        }

        if ($request->filled('login_date_to')) {
            $loginQuery->where('created_at', '<=', Carbon::parse($request->input('login_date_to'))->endOfDay());
        }

        if ($request->filled('login_search')) {
            $loginSearch = $request->input('login_search');
            $loginQuery->where(function ($q) use ($loginSearch) {
                $q->where('ip_address', 'like', "%{$loginSearch}%")
                  ->orWhere('user_agent', 'like', "%{$loginSearch}%")
                  ->orWhereHas('user', function ($uq) use ($loginSearch) {
                      $uq->where('name', 'like', "%{$loginSearch}%")
                         ->orWhere('last_name', 'like', "%{$loginSearch}%")
                         ->orWhere('email', 'like', "%{$loginSearch}%");
                  });
            });
        }

        $loginLogs = $loginQuery->orderByDesc('created_at')->paginate(20, ['*'], 'login_page')->withQueryString();

        return view('admin.audit.index', compact(
            'tab',
            'metrics',
            'users',
            'modelOptions',
            'auditLogs',
            'loginLogs'
        ));
    }
}
