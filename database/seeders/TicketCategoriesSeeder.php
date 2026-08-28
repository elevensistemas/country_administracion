<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TicketCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            TicketCategory::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName]
            );
        }
    }
}
