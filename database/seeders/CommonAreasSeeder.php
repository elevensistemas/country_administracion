<?php

namespace Database\Seeders;

use App\Models\CommonArea;
use Illuminate\Database\Seeder;

class CommonAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CommonArea::firstOrCreate(
            ['name' => 'Quincho'],
            [
                'description' => 'Quincho principal del barrio para eventos y reuniones',
                'capacity' => 50,
                'is_active' => true,
                'price' => 0.00,
                'requires_approval' => false,
                'rules' => 'Respetar los horarios y el estado de limpieza del lugar.',
                'schedule_start' => '08:00:00',
                'schedule_end' => '02:00:00',
                'duration_minutes' => 360,
                'photos' => ['img/common_area_placeholder.jpg'],
            ]
        );
    }
}
