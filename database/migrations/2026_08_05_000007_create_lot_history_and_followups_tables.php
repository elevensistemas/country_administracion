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
        Schema::create('lot_history_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::create('lot_history_event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::create('lot_history_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained()->onDelete('cascade');
            $table->foreignId('functional_unit_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('event_type_id')->constrained('lot_history_event_types')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('lot_history_categories')->onDelete('cascade');
            $table->string('related_model_type')->nullable(); // Polymorphic relation to Expense, Payment, Ticket, Note, etc.
            $table->unsignedBigInteger('related_model_id')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('set null'); // Owner at that moment
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null'); // Tenant at that moment
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User that made the action
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('event_date');
            $table->string('status')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->string('source_channel')->default('portal'); // portal, admin, email, whatsapp, import
            $table->string('visibility')->default('internal'); // internal (admin/staff), public (everyone, including owner/tenant), board_only
            $table->boolean('is_confidential')->default(false);
            $table->longText('metadata')->nullable(); // JSON metadata
            $table->timestamps();
            $table->softDeletes();

            $table->index(['related_model_type', 'related_model_id']);
        });

        Schema::create('lot_history_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_history_event_id')->constrained('lot_history_events')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();
        });

        Schema::create('lot_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_history_event_id')->nullable()->constrained('lot_history_events')->onDelete('cascade');
            $table->foreignId('lot_id')->constrained()->onDelete('cascade');
            $table->string('reason');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null'); // Responsible admin
            $table->date('due_date');
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('pending'); // pending, in_progress, waiting_response, completed, cancelled
            $table->boolean('reminder_sent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_follow_ups');
        Schema::dropIfExists('lot_history_attachments');
        Schema::dropIfExists('lot_history_events');
        Schema::dropIfExists('lot_history_event_types');
        Schema::dropIfExists('lot_history_categories');
    }
};
