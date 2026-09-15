<?php

namespace Database\Seeders;

use App\Models\BillingPeriod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BillingPeriodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentPeriod = Carbon::now()->format('Y-m');
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        BillingPeriod::firstOrCreate(
            ['period' => $currentPeriod],
            [
                'start_date' => $startOfMonth,
                'end_date' => $endOfMonth,
                'status' => 'draft',
            ]
        );
    }
}
