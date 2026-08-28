<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\SupplierInvoice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SupplierInvoicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sup1 = Supplier::where('cuit', '30-55444333-8')->first(); // Seguridad
        $sup2 = Supplier::where('cuit', '30-66777888-2')->first(); // Limpieza
        $sup3 = Supplier::where('cuit', '20-18456123-5')->first(); // Jardines
        $sup4 = Supplier::where('cuit', '30-70722243-5')->first(); // Edesur

        if (!$sup1 || !$sup2 || !$sup3 || !$sup4) {
            // If suppliers don't exist yet, we can't seed invoices.
            return;
        }

        $today = Carbon::today();

        // Overdue (Vencida Impaga - 1 día)
        SupplierInvoice::firstOrCreate(
            ['invoice_number' => '0003-00012489'],
            [
                'supplier_id' => $sup2->id,
                'concept' => 'Abono Limpieza Quincena 1 Agosto',
                'amount' => 125000.00,
                'issue_date' => $today->copy()->subDays(15),
                'due_date' => $today->copy()->subDays(1),
                'status' => 'pending',
                'notes' => 'Factura pendiente de aprobación final por gerencia.',
            ]
        );

        // This Week (Vence esta semana +4 días)
        SupplierInvoice::firstOrCreate(
            ['invoice_number' => '0001-00054231'],
            [
                'supplier_id' => $sup1->id,
                'concept' => 'Abono Servicio Seguridad Agosto',
                'amount' => 250000.00,
                'issue_date' => $today->copy()->subDays(2),
                'due_date' => $today->copy()->addDays(4),
                'status' => 'scheduled',
                'notes' => 'Pago programado para el día viernes.',
            ]
        );

        // Next Week (Vence próxima semana +10 días)
        SupplierInvoice::firstOrCreate(
            ['invoice_number' => '0002-00004123'],
            [
                'supplier_id' => $sup3->id,
                'concept' => 'Servicio de poda y mantenimiento de cerco perimetral',
                'amount' => 85000.00,
                'issue_date' => $today->copy()->subDays(5),
                'due_date' => $today->copy()->addDays(10),
                'status' => 'pending',
            ]
        );

        // Future Previsión (+25 días)
        SupplierInvoice::firstOrCreate(
            ['invoice_number' => '0012-99887766'],
            [
                'supplier_id' => $sup4->id,
                'concept' => 'Factura de luz espacios comunes y portería',
                'amount' => 198000.00,
                'issue_date' => $today->copy()->subDays(1),
                'due_date' => $today->copy()->addDays(25),
                'status' => 'pending',
            ]
        );

        // Paid (Ya abonada en el pasado)
        SupplierInvoice::firstOrCreate(
            ['invoice_number' => '0001-00053912'],
            [
                'supplier_id' => $sup1->id,
                'concept' => 'Abono Servicio Seguridad Julio',
                'amount' => 250000.00,
                'issue_date' => $today->copy()->subDays(32),
                'due_date' => $today->copy()->subDays(5),
                'status' => 'paid',
                'notes' => 'Pagado con transferencia de cuenta Banco Provincia.',
            ]
        );
    }
}
