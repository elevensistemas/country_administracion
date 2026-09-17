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
                'name' => 'Administración',
                'last_name' => '1',
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
                'name' => 'Administración',
                'last_name' => '2',
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
                'name' => 'Contabilidad',
                'last_name' => 'General',
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
                'name' => 'Operador',
                'last_name' => '1',
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

        // 6. Country Administration Team (10 Operadores con control total salvo creación de usuarios)
        $team = [
            ['name' => 'Cora', 'last_name' => 'Barrera', 'email' => 'c.barrera@laranita.com', 'phone' => '+5491140010001', 'dni' => '30000001'],
            ['name' => 'Camila', 'last_name' => 'Gonzalez', 'email' => 'c.gonzalez@laranita.com', 'phone' => '+5491140010002', 'dni' => '30000002'],
            ['name' => 'Maria', 'last_name' => 'Romero', 'email' => 'm.romero@laranita.com', 'phone' => '+5491140010003', 'dni' => '30000003'],
            ['name' => 'Camila', 'last_name' => 'Soria', 'email' => 'c.soria@laranita.com', 'phone' => '+5491140010004', 'dni' => '30000004'],
            ['name' => 'Lara', 'last_name' => 'Garcia', 'email' => 'l.garcia@laranita.com', 'phone' => '+5491140010005', 'dni' => '30000005'],
            ['name' => 'Juan', 'last_name' => 'Villafañe', 'email' => 'j.villafane@laranita.com', 'phone' => '+5491140010006', 'dni' => '30000006'],
            ['name' => 'Ignacio', 'last_name' => 'Villalfañe', 'email' => 'i.villalfane@laranita.com', 'phone' => '+5491140010007', 'dni' => '30000007'],
            ['name' => 'Federico', 'last_name' => 'Chichirico', 'email' => 'f.chichirico@laranita.com', 'phone' => '+5491140010008', 'dni' => '30000008'],
            ['name' => 'Lorena', 'last_name' => 'La Manna', 'email' => 'l.lamanna@laranita.com', 'phone' => '+5491140010009', 'dni' => '30000009'],
            ['name' => 'Martin', 'last_name' => 'Corbalan', 'email' => 'm.corbalan@laranita.com', 'phone' => '+5491140010010', 'dni' => '30000010'],
        ];

        foreach ($team as $member) {
            $user = User::where('email', $member['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $member['name'],
                    'last_name' => $member['last_name'],
                    'email' => $member['email'],
                    'phone' => $member['phone'],
                    'dni' => $member['dni'],
                    'status' => 'active',
                    'relationship_type' => 'operator',
                    'password' => Hash::make('alguna123'),
                    'email_verified_at' => now(),
                    'login_count' => 0,
                    'terms_accepted_at' => now(),
                ]);
            } else {
                $user->update([
                    'password' => Hash::make('alguna123'),
                    'status' => 'active',
                    'relationship_type' => 'operator',
                ]);
            }

            if ($roleOperator && !$user->roles()->where('name', 'operator')->exists()) {
                $user->roles()->sync([$roleOperator->id]);
            }
        }
    }
}
