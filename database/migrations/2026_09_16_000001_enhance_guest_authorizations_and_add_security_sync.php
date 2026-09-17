<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Upgrade guest_authorizations
        Schema::table('guest_authorizations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->dateTime('valid_from')->nullable()->after('visit_time');
            $table->dateTime('valid_until')->nullable()->after('valid_from');
            $table->timestamp('cancelled_at')->nullable()->after('status');
            $table->string('status', 30)->default('active')->change();
        });

        // Backfill UUID and validity for existing records
        $records = DB::table('guest_authorizations')->get();
        foreach ($records as $r) {
            $validFrom = $r->visit_date ? ($r->visit_time ? "{$r->visit_date} {$r->visit_time}" : "{$r->visit_date} 00:00:00") : null;
            $validUntil = $r->visit_date ? "{$r->visit_date} 23:59:59" : null;

            DB::table('guest_authorizations')
                ->where('id', $r->id)
                ->update([
                    'uuid' => (string) Str::uuid(),
                    'valid_from' => $validFrom,
                    'valid_until' => $validUntil,
                ]);
        }

        // Add index on uuid
        Schema::table('guest_authorizations', function (Blueprint $table) {
            $table->unique('uuid');
            $table->index(['updated_at', 'id']);
            $table->index('dni');
            $table->index('license_plate');
        });

        // 2. Security Devices Table
        Schema::create('security_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('device_identifier')->unique();
            $table->string('api_token_hash');
            $table->timestamp('last_seen_at')->nullable();
            $table->string('last_sync_cursor')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Security Sync Logs Table
        Schema::create('security_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_device_id')->constrained('security_devices')->onDelete('cascade');
            $table->string('since_cursor')->nullable();
            $table->integer('records_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('status', 20)->default('success');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_sync_logs');
        Schema::dropIfExists('security_devices');

        Schema::table('guest_authorizations', function (Blueprint $table) {
            $table->dropIndex(['guest_authorizations_uuid_unique']);
            $table->dropIndex(['updated_at', 'id']);
            $table->dropIndex(['dni']);
            $table->dropIndex(['license_plate']);
            $table->dropColumn(['uuid', 'valid_from', 'valid_until', 'cancelled_at']);
        });
    }
};