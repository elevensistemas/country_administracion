<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'business_name' => 'Seguridad y Monitoreo del Norte S.A.',
                'cuit' => '30-55444333-8',
                'category' => 'Seguridad',
                'email' => 'administracion@seguridadnorte.com',
                'phone' => '+5491155667788',
                'address' => 'Av. General Paz 4500, CABA',
                'bank_name' => 'Banco de la Nación Argentina',
                'cbu_alias' => 'seguridad.norte.cbu',
                'status' => 'active',
            ],
            [
                'business_name' => 'Servicios Generales Limpieza S.R.L.',
                'cuit' => '30-66777888-2',
                'category' => 'Mantenimiento y Limpieza',
                'email' => 'proveedores@limpiezasrl.com',
                'phone' => '+5491144332211',
                'address' => 'Sarmiento 1200, Pilar',
                'bank_name' => 'Banco Santander Río',
                'cbu_alias' => 'limpieza.laranita.alias',
                'status' => 'active',
            ],
            [
                'business_name' => 'Jardines y Parquizaciones Verdes',
                'cuit' => '20-18456123-5',
                'category' => 'Jardinería',
                'email' => 'contacto@jardinesverdes.com',
                'phone' => '+5491133225566',
                'address' => 'Ruta 8 Km 54, Pilar',
                'bank_name' => 'Banco Galicia',
                'cbu_alias' => 'jardines.galicia.alias',
                'status' => 'active',
            ],
            [
                'business_name' => 'Edesur S.A.',
                'cuit' => '30-70722243-5',
                'category' => 'Electricidad',
                'email' => 'facturacion@edesur.com.ar',
                'phone' => '0810-222-0200',
                'address' => 'San José 140, CABA',
                'bank_name' => 'Banco BBVA Argentina',
                'cbu_alias' => 'edesur.pago.cbu',
                'status' => 'active',
            ]
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::firstOrCreate(
                ['cuit' => $supplierData['cuit']],
                $supplierData
            );
        }
    }
}
