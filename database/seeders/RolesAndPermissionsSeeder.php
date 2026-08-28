<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'superadmin' => 'Super Administrador',
            'admin' => 'Administrador',
            'operator' => 'Operador Administrativo',
            'accounting' => 'Contabilidad',
            'owner' => 'Propietario',
            'tenant' => 'Inquilino',
            'board' => 'Consejo o Directorio',
        ];

        $roleModels = [];
        foreach ($roles as $name => $displayName) {
            $roleModels[$name] = Role::firstOrCreate(
                ['name' => $name],
                [
                    'display_name' => $displayName,
                    'description' => "Rol de $displayName",
                ]
            );
        }

        $permissions = [
            'manage-users' => 'Gestionar Usuarios',
            'manage-lots' => 'Gestionar Lotes y Unidades',
            'manage-finances' => 'Gestionar Finanzas y Expensas',
            'manage-payments' => 'Conciliar y Aprobar Pagos',
            'manage-tickets' => 'Responder Reclamos',
            'manage-communications' => 'Enviar Comunicados',
            'view-reports' => 'Ver Reportes y Métricas',
            'view-audit' => 'Consultar Logs y Auditoría',
            'use-portal' => 'Acceso al Portal del Propietario',
        ];

        $permissionModels = [];
        foreach ($permissions as $name => $displayName) {
            $permissionModels[$name] = Permission::firstOrCreate(
                ['name' => $name],
                [
                    'display_name' => $displayName,
                    'description' => "Permiso para $displayName",
                ]
            );
        }

        // Get IDs of all permissions
        $allPermissionIds = collect($permissionModels)->pluck('id')->toArray();

        // Assign Permissions to Roles
        $roleModels['superadmin']->permissions()->sync($allPermissionIds);
        $roleModels['admin']->permissions()->sync([
            $permissionModels['manage-users']->id,
            $permissionModels['manage-lots']->id,
            $permissionModels['manage-finances']->id,
            $permissionModels['manage-payments']->id,
            $permissionModels['manage-tickets']->id,
            $permissionModels['manage-communications']->id,
            $permissionModels['view-reports']->id,
        ]);
        $roleModels['operator']->permissions()->sync([
            $permissionModels['manage-lots']->id,
            $permissionModels['manage-tickets']->id,
            $permissionModels['manage-communications']->id,
            $permissionModels['use-portal']->id,
        ]);
        $roleModels['accounting']->permissions()->sync([
            $permissionModels['manage-finances']->id,
            $permissionModels['manage-payments']->id,
            $permissionModels['view-reports']->id,
        ]);
        $roleModels['owner']->permissions()->sync([$permissionModels['use-portal']->id]);
        $roleModels['tenant']->permissions()->sync([$permissionModels['use-portal']->id]);
        $roleModels['board']->permissions()->sync([
            $permissionModels['view-reports']->id,
            $permissionModels['use-portal']->id,
        ]);
    }
}
