<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\FunctionalUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'functionalUnits']);

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Filter by Role
        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            })->orWhere('relationship_type', $role);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->orderBy('last_name')->orderBy('name')->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $functionalUnits = FunctionalUnit::with('lot')->get();
        return view('admin.users.create', compact('roles', 'functionalUnits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'dni' => 'nullable|string',
            'relationship_type' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'functional_unit_ids' => 'nullable|array',
            'functional_unit_ids.*' => 'exists:functional_units,id',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            // Generate a random temporary password
            $tempPassword = Str::random(12);

            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'dni' => $request->dni,
                'relationship_type' => $request->relationship_type,
                'status' => 'pending_invite',
                'password' => Hash::make($tempPassword), // Has default temporary password
                'notes' => $request->notes,
                'hire_date' => now(),
            ]);

            // Attach Role
            $user->roles()->attach($request->role_id);

            // Attach Functional Units
            if ($request->filled('functional_unit_ids')) {
                foreach ($request->functional_unit_ids as $fuId) {
                    $user->functionalUnits()->attach($fuId, [
                        'relationship_type' => $request->relationship_type,
                    ]);
                }
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente. Se ha generado la invitación inicial.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $functionalUnits = FunctionalUnit::with('lot')->get();
        $associatedUnits = $user->functionalUnits->pluck('id')->toArray();
        $userRoleId = $user->roles->first()?->id;

        return view('admin.users.edit', compact('user', 'roles', 'functionalUnits', 'associatedUnits', 'userRoleId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'dni' => 'nullable|string',
            'relationship_type' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'functional_unit_ids' => 'nullable|array',
            'functional_unit_ids.*' => 'exists:functional_units,id',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'dni' => $request->dni,
                'relationship_type' => $request->relationship_type,
                'notes' => $request->notes,
            ]);

            // Sync Role
            $user->roles()->sync([$request->role_id]);

            // Sync Functional Units
            $syncData = [];
            if ($request->filled('functional_unit_ids')) {
                foreach ($request->functional_unit_ids as $fuId) {
                    $syncData[$fuId] = ['relationship_type' => $request->relationship_type];
                }
            }
            $user->functionalUnits()->sync($syncData);
        });

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Toggle active/blocked status.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar el estado de tu propio usuario.');
        }

        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();

        $statusText = $user->status === 'active' ? 'activado' : 'bloqueado';
        return back()->with('success', "Usuario {$statusText} correctamente.");
    }

    /**
     * Resend invitation link.
     */
    public function resendInvite(User $user)
    {
        // Resets password to temp and logs invitation sent
        $tempPassword = Str::random(12);
        $user->password = Hash::make($tempPassword);
        $user->status = 'pending_invite';
        $user->terms_accepted_at = null;
        $user->login_count = 0;
        $user->save();

        return back()->with('success', "Enlace de invitación reenviado a {$user->email}. (Simulado)");
    }
}
