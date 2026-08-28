<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles from database
        $roleSuperadmin = Role::where('name', 'superadmin')->first();
        $roleAdmin = Role::where('name', 'admin')->first();
        $roleAccounting = Role::where('name', 'accounting')->first();
        $roleOperator = Role::where('name', 'operator')->first();

        // 1. Super Admin
        $superAdmin = User::where('email', 'superadmin@laranita.com')->first();
        if (!$superAdmin) {
            $superAdmin = User::create([
                'name' => 'Alejandro',
                'last_name' => 'Lo Presti',
                'email' => 'superadmin@laranita.com',
                'phone' => '+5491133334444',
                'dni' => '11222333',
                'status' => 'active',
                'relationship_type' => 'superadmin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'login_count' => 1,
                'last_login_at' => now(),
                'first_login_at' => now(),
                'terms_accepted_at' => now(),
            ]);
        }
        if ($roleSuperadmin && !$superAdmin->roles()->where('name', 'superadmin')->exists()) {
            $superAdmin->roles()->attach($roleSuperadmin->id);
        }

        // 2. Admin 1
        $admin1 = User::where('email', 'admin1@laranita.com')->first();
        if (!$admin1) {
            $admin1 = User::create([
                'name' => 'María Marta',
                'last_name' => 'Fernández',
                'email' => 'admin1@laranita.com',
                'phone' => '+5491144445555',
                'dni' => '22333444',
                'status' => 'active',
                'relationship_type' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'login_count' => 1,
                'last_login_at' => now(),
                'first_login_at' => now(),
                'terms_accepted_at' => now(),
            ]);
        }
        if ($roleAdmin && !$admin1->roles()->where('name', 'admin')->exists()) {
            $admin1->roles()->attach($roleAdmin->id);
        }

        // 3. Admin 2
        $admin2 = User::where('email', 'admin2@laranita.com')->first();
        if (!$admin2) {
            $admin2 = User::create([
                'name' => 'Juan Carlos',
                'last_name' => 'Pérez',
                'email' => 'admin2@laranita.com',
                'phone' => '+5491155556666',
                'dni' => '33444555',
                'status' => 'active',
                'relationship_type' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'login_count' => 1,
                'last_login_at' => now(),
                'first_login_at' => now(),
                'terms_accepted_at' => now(),
            ]);
        }
        if ($roleAdmin && !$admin2->roles()->where('name', 'admin')->exists()) {
            $admin2->roles()->attach($roleAdmin->id);
        }

        // 4. Accounting
        $accounting = User::where('email', 'contabilidad@laranita.com')->first();
        if (!$accounting) {
            $accounting = User::create([
                'name' => 'Esteban',
                'last_name' => 'Gómez',
                'email' => 'contabilidad@laranita.com',
                'phone' => '+5491166667777',
                'dni' => '44555666',
                'status' => 'active',
                'relationship_type' => 'accounting',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'login_count' => 1,
                'last_login_at' => now(),
                'first_login_at' => now(),
                'terms_accepted_at' => now(),
            ]);
        }
        if ($roleAccounting && !$accounting->roles()->where('name', 'accounting')->exists()) {
            $accounting->roles()->attach($roleAccounting->id);
        }

        // 5. Operator 1
        $operator1 = User::where('email', 'operador1@laranita.com')->first();
        if (!$operator1) {
            $operator1 = User::create([
                'name' => 'Ramiro',
                'last_name' => 'López',
                'email' => 'operador1@laranita.com',
                'phone' => '+5491177778888',
                'dni' => '55666777',
                'status' => 'active',
                'relationship_type' => 'operator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'login_count' => 1,
                'last_login_at' => now(),
                'first_login_at' => now(),
                'terms_accepted_at' => now(),
            ]);
        }
        if ($roleOperator && !$operator1->roles()->where('name', 'operator')->exists()) {
            $operator1->roles()->attach($roleOperator->id);
        }
    }
}
