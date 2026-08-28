<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docCats = [
            'regulations' => 'Reglamentos e Internas',
            'minutes' => 'Actas de Asambleas',
            'expenses_liq' => 'Liquidación de Expensas',
            'forms' => 'Formularios y Autorizaciones',
            'legal' => 'Información Legal',
        ];

        foreach ($docCats as $name => $displayName) {
            DocumentCategory::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName]
            );
        }
    }
}
