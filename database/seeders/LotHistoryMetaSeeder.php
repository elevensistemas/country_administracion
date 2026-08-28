<?php

namespace Database\Seeders;

use App\Models\LotHistoryCategory;
use App\Models\LotHistoryEventType;
use Illuminate\Database\Seeder;

class LotHistoryMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $histCats = [
            'admin' => 'Administrativo',
            'finance' => 'Financiero',
            'security' => 'Seguridad',
            'maintenance' => 'Mantenimiento',
            'inspections' => 'Inspecciones y Sanciones',
            'comms' => 'Comunicaciones',
        ];

        foreach ($histCats as $name => $displayName) {
            LotHistoryCategory::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName]
            );
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

        foreach ($histTypes as $name => $displayName) {
            LotHistoryEventType::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName]
            );
        }
    }
}
