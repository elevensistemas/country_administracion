<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Owner;
use App\Models\Lot;
use App\Models\FunctionalUnit;
use App\Models\UserPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class RealLotsAndOwnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerRole = Role::where('name', 'owner')->first();
        $ownerRoleId = $ownerRole ? $ownerRole->id : null;

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
                $lot = Lot::where('number', $lotNum)->first();
                if (!$lot) {
                    $lot = Lot::create([
                        'number' => $lotNum,
                        'code' => 'LOT-' . str_pad($lotNum, 3, '0', STR_PAD_LEFT),
                        'name' => "Lote {$lotNum}",
                        'internal_address' => "Calle Principal Lote {$lotNum}",
                        'status' => 'active',
                        'current_owner_id' => $owner->id,
                        'balance' => 0.00,
                    ]);
                }

                // 7.3 Create Functional Unit
                $fu = FunctionalUnit::where('lot_id', $lot->id)->first();
                if (!$fu) {
                    $fu = FunctionalUnit::create([
                        'lot_id' => $lot->id,
                        'code' => $lot->code . '-UF',
                        'name' => "UF Lote {$lotNum}",
                        'description' => "Unidad funcional del Lote {$lotNum}",
                        'balance' => 0.00,
                    ]);
                }

                // Associate Owner to Functional Unit if not already associated
                if (!$owner->functionalUnits()->where('functional_unit_id', $fu->id)->exists()) {
                    $owner->functionalUnits()->attach($fu->id, ['share_percentage' => 100]);
                }

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
                        'password' => Hash::make('alguna123'), // Default password
                    ]);
                    
                    if ($ownerRoleId) {
                        $user->roles()->attach($ownerRoleId);
                    }

                    // Create User Preference
                    UserPreference::create([
                        'user_id' => $user->id,
                        'theme' => 'auto',
                        'notifications_email' => true,
                        'notifications_whatsapp' => (bool)$phone,
                    ]);
                }
                
                if (!$user->functionalUnits()->where('functional_unit_id', $fu->id)->exists()) {
                    $user->functionalUnits()->attach($fu->id, ['relationship_type' => 'owner']);
                }
            }
        }
    }
}
