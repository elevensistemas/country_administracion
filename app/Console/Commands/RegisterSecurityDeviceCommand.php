<?php

namespace App\Console\Commands;

use App\Models\SecurityDevice;
use Illuminate\Console\Command;

class RegisterSecurityDeviceCommand extends Command
{
    protected $signature = 'security:device 
                            {action=list : Action to perform (list, create, revoke)}
                            {--name= : Name of the device / guardhouse}
                            {--identifier= : Unique identifier (e.g. GARITA-01)}
                            {--notes= : Optional notes}';

    protected $description = 'Manage security devices and tokens for guardhouse synchronization';

    public function handle(): int
    {
        $action = $this->argument('action');

        if ($action === 'create') {
            $name = $this->option('name') ?: $this->ask('Nombre del dispositivo (ej: Garita Principal)');
            $identifier = $this->option('identifier') ?: $this->ask('Identificador único (ej: GARITA-01)');
            $notes = $this->option('notes');

            $result = SecurityDevice::createWithToken($name, $identifier, $notes);
            
            $this->info('====================================================');
            $this->info('  Dispositivo de Seguridad Creado Exitosamente');
            $this->info('====================================================');
            $this->line('  ID:          ' . $result['device']->id);
            $this->line('  Nombre:      ' . $result['device']->name);
            $this->line('  Código:      ' . $result['device']->device_identifier);
            $this->warn('  TOKEN SECRETO (Copiar ahora, no se vuelve a mostrar):');
            $this->line('  ' . $result['plain_token']);
            $this->info('====================================================');
            return 0;
        }

        if ($action === 'revoke') {
            $identifier = $this->option('identifier') ?: $this->ask('Identificador o ID del dispositivo a revocar');
            $device = SecurityDevice::where('device_identifier', $identifier)->orWhere('id', $identifier)->first();
            if (!$device) {
                $this->error("No se encontró el dispositivo: {$identifier}");
                return 1;
            }

            $device->update([
                'is_active' => false,
                'revoked_at' => now(),
            ]);

            $this->warn("Dispositivo {$device->name} ({$device->device_identifier}) REVOCADO.");
            return 0;
        }

        // List
        $devices = SecurityDevice::all();
        if ($devices->isEmpty()) {
            $this->warn('No hay dispositivos de seguridad registrados.');
            $this->line('Para crear uno: php artisan security:device create --name="Garita Principal" --identifier="GARITA-01"');
            return 0;
        }

        $this->table(
            ['ID', 'Nombre', 'Identificador', 'Activo', 'Última Conexión', 'Último Cursor'],
            $devices->map(fn($d) => [
                $d->id,
                $d->name,
                $d->device_identifier,
                $d->is_active ? 'Sí' : 'No (Revocado)',
                $d->last_seen_at ? $d->last_seen_at->toDateTimeString() : 'Nunca',
                $d->last_sync_cursor ?: 'N/A'
            ])
        );

        return 0;
    }
}