<?php

namespace Database\Seeders;

use App\Models\GuestAuthorization;
use App\Models\Lot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuestAuthorizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lot = Lot::first();
        $user = User::whereHas('functionalUnits')->first() ?? User::first();

        if ($lot && $user) {
            GuestAuthorization::firstOrCreate(
                ['dni' => '12121212'],
                [
                    'lot_id' => $lot->id,
                    'user_id' => $user->id,
                    'type' => 'individual',
                    'name' => 'Nicole',
                    'last_name' => 'Nunes',
                    'visit_date' => Carbon::now()->toDateString(),
                    'visit_time' => '14:00:00',
                    'status' => 'active',
                    'qr_code' => 'RANITA-' . strtoupper(Str::random(12)),
                ]
            );

            GuestAuthorization::firstOrCreate(
                ['dni' => '131313131'],
                [
                    'lot_id' => $lot->id,
                    'user_id' => $user->id,
                    'type' => 'individual',
                    'name' => 'Juan',
                    'last_name' => 'Salaverri',
                    'visit_date' => Carbon::now()->toDateString(),
                    'visit_time' => null,
                    'status' => 'active',
                    'qr_code' => 'RANITA-' . strtoupper(Str::random(12)),
                ]
            );
        }
    }
}
