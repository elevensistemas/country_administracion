<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\UserPreference;
use App\Models\Owner;
use App\Models\Lot;
use App\Models\FunctionalUnit;
use App\Models\LotHistoryCategory;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryEvent;
use App\Models\TicketCategory;
use App\Models\DocumentCategory;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions (System Meta)
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
            $roleModels[$name] = Role::create([
                'name' => $name,
                'display_name' => $displayName,
                'description' => "Rol de $displayName",
            ]);
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
            $permissionModels[$name] = Permission::create([
                'name' => $name,
                'display_name' => $displayName,
                'description' => "Permiso para $displayName",
            ]);
        }

        // Assign Permissions to Roles
        $roleModels['superadmin']->permissions()->sync(array_values($permissionModels));
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

        // 2. Administrative Users (System Operators)
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
        $superAdmin->roles()->attach($roleModels['superadmin']->id);

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
        $admin1->roles()->attach($roleModels['admin']->id);

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
        $admin2->roles()->attach($roleModels['admin']->id);

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
        $accounting->roles()->attach($roleModels['accounting']->id);

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
        $operator1->roles()->attach($roleModels['operator']->id);

        // 3. Lot History Event Types & Categories
        $histCats = [
            'admin' => 'Administrativo',
            'finance' => 'Financiero',
            'security' => 'Seguridad',
            'maintenance' => 'Mantenimiento',
            'inspections' => 'Inspecciones y Sanciones',
            'comms' => 'Comunicaciones',
        ];
        $histCatModels = [];
        foreach ($histCats as $name => $displayName) {
            $histCatModels[$name] = LotHistoryCategory::create([
                'name' => $name,
                'display_name' => $displayName,
            ]);
        }

        $histTypes = [
            'lot_created' => 'Alta de Lote',
            'owner_changed' => 'Cambio de Propietario',
            'tenant_changed' => 'Cambio de Inquilino',
            'user_associated' => 'Usuario Asociado',
            'user_dissociated' => 'Usuario Desasociado',
            'expense_generated' => 'Expensa Generada',
            'expense_published' => 'Expensa Publicada',
            'payment_reported' => 'Pago Informado',
            'payment_approved' => 'Pago Aprobado',
            'payment_rejected' => 'Pago Rechazado',
            'ticket_created' => 'Reclamo Creado',
            'ticket_answered' => 'Reclamo Respondido',
            'ticket_closed' => 'Reclamo Cerrado',
            'note_added' => 'Nota Administrativa',
            'incident_logged' => 'Incidente de Seguridad',
            'sanction_applied' => 'Sanción Aplicada',
            'inspection_logged' => 'Inspección Realizada',
            'comm_sent' => 'Comunicación Enviada',
        ];
        $histTypeModels = [];
        foreach ($histTypes as $name => $displayName) {
            $histTypeModels[$name] = LotHistoryEventType::create([
                'name' => $name,
                'display_name' => $displayName,
            ]);
        }

        // 4. Ticket Categories
        $ticketCats = [
            'admin' => 'Administración',
            'expenses' => 'Expensas y Cuentas',
            'security' => 'Seguridad',
            'maintenance' => 'Mantenimiento e Infraestructura',
            'poda' => 'Poda y Jardinería',
            'pets' => 'Mascotas',
            'documents' => 'Documentación',
            'suggestions' => 'Sugerencias',
        ];
        foreach ($ticketCats as $name => $displayName) {
            TicketCategory::create([
                'name' => $name,
                'display_name' => $displayName,
            ]);
        }

        // 5. Document Categories
        $docCats = [
            'regulations' => 'Reglamentos e Internas',
            'minutes' => 'Actas de Asambleas',
            'expenses_liq' => 'Liquidación de Expensas',
            'forms' => 'Formularios y Autorizaciones',
            'legal' => 'Información Legal',
        ];
        foreach ($docCats as $name => $displayName) {
            DocumentCategory::create([
                'name' => $name,
                'display_name' => $displayName,
            ]);
        }

        // 6. System Settings
        $settings = [
            'neighborhood_name' => ['Barrio Privado La Ranita', 'Nombre comercial de la urbanización'],
            'cuit' => ['30-71234567-9', 'CUIT institucional del consorcio'],
            'address' => ['Ruta 25 Km 4.5, Pilar, Buenos Aires', 'Dirección física del barrio'],
            'interest_rate_monthly' => ['3.5', 'Porcentaje de interés mensual por mora (en %)'],
            'due_day' => ['10', 'Día del mes para el primer vencimiento de expensas'],
            'second_due_day' => ['20', 'Día del mes para el segundo vencimiento de expensas'],
            'interest_type' => ['daily', 'Tipo de cálculo de interés: daily (diario) o monthly (mensual completo)'],
            'notify_reservation_email' => ['1', 'Enviar notificación por email de nuevas reservas al administrador (1: Si, 0: No)'],
            'notify_reservation_system' => ['1', 'Mostrar alertas en la campana de notificaciones de nuevas reservas (1: Si, 0: No)'],
            'notify_reservation_owner_email' => ['1', 'Enviar confirmación/estado por email al propietario (1: Si, 0: No)'],
        ];
        foreach ($settings as $key => $values) {
            SystemSetting::create([
                'key' => $key,
                'value' => $values[0],
                'description' => $values[1],
            ]);
        }

        // 7. Seed Real Lots & Owners from extracted JSON
        $jsonPath = database_path('seeders/real_data.json');
        if (File::exists($jsonPath)) {
            $json = File::get($jsonPath);
            $realLots = json_decode($json, true);

            foreach ($realLots as $data) {
                $lotNum = $data['lot_number'];
                $ownerName = $data['owner_name'];
                $phone = $data['phone'];
                $email1 = $data['email1'];
                $email2 = $data['email2'];

                // Separate first name and last name
                $parts = explode(',', $ownerName, 2);
                if (count($parts) === 2) {
                    $lastName = trim($parts[0]);
                    $firstName = trim($parts[1]);
                } else {
                    $partsSpace = explode(' ', $ownerName, 2);
                    if (count($partsSpace) === 2) {
                        $firstName = trim($partsSpace[0]);
                        $lastName = trim($partsSpace[1]);
                    } else {
                        $firstName = $ownerName;
                        $lastName = 'Propietario';
                    }
                }

                // Generate email: initial_first_name.last_name@laranita.com
                $firstLetter = mb_substr(trim($firstName), 0, 1);
                $lastNameClean = trim($lastName);
                
                // Remove accents and special chars
                $replace = [
                    'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
                    'Á'=>'a', 'É'=>'e', 'Í'=>'i', 'Ó'=>'o', 'Ú'=>'u',
                    'ñ'=>'n', 'Ñ'=>'n', 'ü'=>'u', 'Ü'=>'u'
                ];
                $firstLetter = strtr($firstLetter, $replace);
                $lastNameClean = strtr($lastNameClean, $replace);
                
                // Remove spaces and keep alphanumeric only
                $firstLetter = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstLetter));
                $lastNameClean = str_replace(' ', '', $lastNameClean);
                $lastNameClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $lastNameClean));
                
                if (empty($firstLetter) || empty($lastNameClean)) {
                    $primaryEmail = "propietario.lote{$lotNum}@laranita.com";
                } else {
                    $primaryEmail = "{$firstLetter}.{$lastNameClean}@laranita.com";
                }

                $status = ($lotNum === 10) ? 'inactive' : 'active';
                $mockDni = rand(10, 45) . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT) . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                $mockCuit = '20-' . $mockDni . '-' . rand(0, 9);

                // 7.1 Create Owner Record (Check first for duplicate email)
                $owner = Owner::where('email', $primaryEmail)->first();
                if (!$owner) {
                    $owner = Owner::create([
                        'name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $primaryEmail,
                        'phone' => $phone ?: null,
                        'dni' => $mockDni,
                        'cuit' => $mockCuit,
                        'address' => "Lote {$lotNum}",
                        'status' => $status,
                        'preferred_channel' => $phone ? 'both' : 'email',
                    ]);
                }

                // 7.2 Create Lot Record
                $lot = Lot::create([
                    'number' => $lotNum,
                    'code' => 'LOT-' . str_pad($lotNum, 3, '0', STR_PAD_LEFT),
                    'name' => "Lote {$lotNum}",
                    'internal_address' => "Calle Principal Lote {$lotNum}",
                    'status' => 'active',
                    'current_owner_id' => $owner->id,
                    'balance' => 0.00,
                ]);

                // 7.3 Create Functional Unit
                $fu = FunctionalUnit::create([
                    'lot_id' => $lot->id,
                    'code' => $lot->code . '-UF',
                    'name' => "UF Lote {$lotNum}",
                    'description' => "Unidad funcional del Lote {$lotNum}",
                    'balance' => 0.00,
                ]);

                // Associate Owner to Functional Unit
                $owner->functionalUnits()->attach($fu->id, ['share_percentage' => 100]);

                // 7.4 Create Resident User Account (Check first for duplicate email)
                $user = User::where('email', $primaryEmail)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $primaryEmail,
                        'phone' => $phone ?: null,
                        'dni' => $owner->dni ?? $mockDni,
                        'status' => $status, // Set active or inactive
                        'relationship_type' => 'owner',
                        'password' => Hash::make('alguna123'), // Default password requested by user
                    ]);
                    $user->roles()->attach($roleModels['owner']->id);

                    // Create User Preference
                    UserPreference::create([
                        'user_id' => $user->id,
                        'theme' => 'auto',
                        'notifications_email' => true,
                        'notifications_whatsapp' => (bool)$phone,
                    ]);
                }
                $user->functionalUnits()->attach($fu->id, ['relationship_type' => 'owner']);

                // No initial lot history events are seeded to start with a completely empty history log.
            }
        }

        // 8. Seed Suppliers
        $sup1 = \App\Models\Supplier::create([
            'business_name' => 'Seguridad y Monitoreo del Norte S.A.',
            'cuit' => '30-55444333-8',
            'category' => 'Seguridad',
            'email' => 'administracion@seguridadnorte.com',
            'phone' => '+5491155667788',
            'address' => 'Av. General Paz 4500, CABA',
            'bank_name' => 'Banco de la Nación Argentina',
            'cbu_alias' => 'seguridad.norte.cbu',
            'status' => 'active',
        ]);

        $sup2 = \App\Models\Supplier::create([
            'business_name' => 'Servicios Generales Limpieza S.R.L.',
            'cuit' => '30-66777888-2',
            'category' => 'Mantenimiento y Limpieza',
            'email' => 'proveedores@limpiezasrl.com',
            'phone' => '+5491144332211',
            'address' => 'Sarmiento 1200, Pilar',
            'bank_name' => 'Banco Santander Río',
            'cbu_alias' => 'limpieza.laranita.alias',
            'status' => 'active',
        ]);

        $sup3 = \App\Models\Supplier::create([
            'business_name' => 'Jardines y Parquizaciones Verdes',
            'cuit' => '20-18456123-5',
            'category' => 'Jardinería',
            'email' => 'contacto@jardinesverdes.com',
            'phone' => '+5491133225566',
            'address' => 'Ruta 8 Km 54, Pilar',
            'bank_name' => 'Banco Galicia',
            'cbu_alias' => 'jardines.galicia.alias',
            'status' => 'active',
        ]);

        $sup4 = \App\Models\Supplier::create([
            'business_name' => 'Edesur S.A.',
            'cuit' => '30-70722243-5',
            'category' => 'Electricidad',
            'email' => 'facturacion@edesur.com.ar',
            'phone' => '0810-222-0200',
            'address' => 'San José 140, CABA',
            'bank_name' => 'Banco BBVA Argentina',
            'cbu_alias' => 'edesur.pago.cbu',
            'status' => 'active',
        ]);

        // 9. Seed Supplier Invoices
        $today = \Carbon\Carbon::today();

        // Overdue (Vencida Impaga - 1 día)
        \App\Models\SupplierInvoice::create([
            'supplier_id' => $sup2->id,
            'invoice_number' => '0003-00012489',
            'concept' => 'Abono Limpieza Quincena 1 Agosto',
            'amount' => 125000.00,
            'issue_date' => $today->copy()->subDays(15),
            'due_date' => $today->copy()->subDays(1),
            'status' => 'pending',
            'notes' => 'Factura pendiente de aprobación final por gerencia.',
        ]);

        // This Week (Vence esta semana +4 días)
        \App\Models\SupplierInvoice::create([
            'supplier_id' => $sup1->id,
            'invoice_number' => '0001-00054231',
            'concept' => 'Abono Servicio Seguridad Agosto',
            'amount' => 250000.00,
            'issue_date' => $today->copy()->subDays(2),
            'due_date' => $today->copy()->addDays(4),
            'status' => 'scheduled',
            'notes' => 'Pago programado para el día viernes.',
        ]);

        // Next Week (Vence próxima semana +10 días)
        \App\Models\SupplierInvoice::create([
            'supplier_id' => $sup3->id,
            'invoice_number' => '0002-00004123',
            'concept' => 'Servicio de poda y mantenimiento de cerco perimetral',
            'amount' => 85000.00,
            'issue_date' => $today->copy()->subDays(5),
            'due_date' => $today->copy()->addDays(10),
            'status' => 'pending',
        ]);

        // Future Previsión (+25 días)
        \App\Models\SupplierInvoice::create([
            'supplier_id' => $sup4->id,
            'invoice_number' => '0012-99887766',
            'concept' => 'Factura de luz espacios comunes y portería',
            'amount' => 198000.00,
            'issue_date' => $today->copy()->subDays(1),
            'due_date' => $today->copy()->addDays(25),
            'status' => 'pending',
        ]);

        // Paid (Ya abonada en el pasado)
        \App\Models\SupplierInvoice::create([
            'supplier_id' => $sup1->id,
            'invoice_number' => '0001-00053912',
            'concept' => 'Abono Servicio Seguridad Julio',
            'amount' => 250000.00,
            'issue_date' => $today->copy()->subDays(32),
            'due_date' => $today->copy()->subDays(5),
            'status' => 'paid',
            'notes' => 'Pagado con transferencia de cuenta Banco Provincia.',
        ]);
    }
}
