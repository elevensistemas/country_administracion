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
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('business_name')->nullable();
            $table->string('dni')->nullable();
            $table->string('cuit')->nullable();
            $table->string('email')->unique();
            $table->string('email_alternate')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_alternate')->nullable();
            $table->string('address')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->text('notes')->nullable();
            $table->string('preferred_channel')->default('email'); // email, whatsapp, both, portal
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('dni');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('number')->unique();
            $table->string('name')->nullable();
            $table->string('internal_address')->nullable();
            $table->string('status')->default('active'); // active, under_construction, vacant
            $table->foreignId('current_owner_id')->nullable()->constrained('owners')->onDelete('set null');
            $table->foreignId('current_tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('functional_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('owner_functional_unit', function (Blueprint $table) {
            $table->foreignId('owner_id')->constrained()->onDelete('cascade');
            $table->foreignId('functional_unit_id')->constrained()->onDelete('cascade');
            $table->decimal('share_percentage', 5, 2)->default(100.00);
            $table->primary(['owner_id', 'functional_unit_id']);
        });

        Schema::create('user_functional_unit', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('functional_unit_id')->constrained()->onDelete('cascade');
            $table->string('relationship_type')->default('owner'); // owner, tenant, family, occupant
            $table->primary(['user_id', 'functional_unit_id']);
        });

        Schema::create('ownership_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained()->onDelete('cascade');
            $table->foreignId('owner_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('reason')->nullable();
            $table->string('documents')->nullable(); // JSON or path
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // administrative user who registered this
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('tenancy_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('owner_id')->constrained()->onDelete('cascade'); // authorizing owner
            $table->string('documents')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenancy_history');
        Schema::dropIfExists('ownership_history');
        Schema::dropIfExists('user_functional_unit');
        Schema::dropIfExists('owner_functional_unit');
        Schema::dropIfExists('functional_units');
        Schema::dropIfExists('lots');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('owners');
    }
};
