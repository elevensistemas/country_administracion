<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $values[0],
                    'description' => $values[1],
                ]
            );
        }
    }
}
