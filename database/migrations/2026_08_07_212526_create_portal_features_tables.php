<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Zonas Comunes
        Schema::create('common_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('capacity')->default(20);
            $table->boolean('is_active')->default(true);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('requires_approval')->default(false);
            $table->text('rules')->nullable();
            $table->time('schedule_start')->default('08:00:00');
            $table->time('schedule_end')->default('22:00:00');
            $table->integer('duration_minutes')->default(120);
            $table->json('maintenance_blocked_days')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
        });

        // 2. Reservas
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('common_area_id')->constrained('common_areas')->onDelete('cascade');
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('charge_to_expenses')->default(true);
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'canceled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Autorizaciones de Invitados
        Schema::create('guest_authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['individual', 'list', 'frequent'])->default('individual');
            $table->string('name');
            $table->string('last_name');
            $table->string('dni')->nullable();
            $table->string('license_plate')->nullable();
            $table->date('visit_date')->nullable();
            $table->time('visit_time')->nullable();
            $table->enum('status', ['pending', 'active', 'used', 'expired'])->default('active');
            $table->text('notes')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });

        // 4. Residentes por Lote
        Schema::create('lot_residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->string('name');
            $table->string('last_name');
            $table->string('dni')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('relationship')->nullable();
            $table->timestamps();
        });

        // 5. Vehículos por Lote
        Schema::create('lot_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->string('license_plate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_vehicles');
        Schema::dropIfExists('lot_residents');
        Schema::dropIfExists('guest_authorizations');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('common_areas');
    }
};
